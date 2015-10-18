<?php
namespace Eardish\AuthService;

use Eardish\AuthService\Core\AbstractService;
use Eardish\AuthService\Core\Connection;
use Eardish\Exceptions\EDException;
use Monolog\Logger;

class AuthService extends AbstractService
{
    protected $port;
    protected $addr;

    /**
     * @var Logger
     */
    protected $logger;
    protected $options;

    /**
     * @var Connection
     */
    protected $conn;


    public function __construct(Connection $conn, $kernel, $agentConfig) {
        parent::__construct($conn, $kernel, $agentConfig);

        $this->conn = $conn;
        $this->options = array('cost' => 11);
    }

    public function hashPass($password, $serviceId)
    {
        $this->serviceKernel->register([
            function() use ($serviceId, $password) {
                $hpw = password_hash($password, PASSWORD_DEFAULT, $this->options);

                return ['data' => ['hashPass' => $hpw]];
            }
        ], $serviceId);

        $this->serviceKernel->first($serviceId);
    }

    // Pull down UserId to compare against
    private function getUserIdByEmail($email, $serviceId)
    {
        $config = $this->generateConfigArray(__FUNCTION__, 'select', $serviceId);
        $config['data']['user']['email'] = $email;

        $this->send($config);
    }

    private function getLoginProfileInfo($userId, $serviceId)
    {
        $config = $this->generateConfigArray(__FUNCTION__, 'select', $serviceId);
        $config['data']['profile']['user_id'] = $userId;

        $this->send($config);
    }

    public function getHashPass($email, $password, $serviceId)
    {
        $config = $this->generateConfigArray(__FUNCTION__, 'select', $serviceId);

        $config['data']['user']['email'] = $email;
        $config['data']['user']['password'] = $password;

        $this->send($config);
    }

    public function authenticate($email, $password, $serviceId)
    {
        $email = strtolower($email);

        $this->serviceKernel->register([
            function() use ($email, $password, $serviceId) {
                $this->getHashPass($email, $password, $serviceId);
            },
            function($response, $previousIndex) use ($serviceId, $password, $email) {
                if ($response['data'][$previousIndex]) {
                     $hashPass = $response['data'][$previousIndex][0]['password'];
                } else {
                    $hashPass = 0;
                }
                $this->serviceKernel->setVariable($serviceId, 'hashPass', $hashPass);
                if (password_verify($password,$hashPass)) {
                    $this->getUserIdByEmail($email, $serviceId);
                } else {
                    $this->serviceKernel->next(['serviceId' => $serviceId], true);
                }
            },
            function($response, $previousIndex) use ($serviceId) {
                if ($response['data'][$previousIndex]) {
                    $success = true;
                    $userId = $response['data'][$previousIndex][0]['id'];
                } else {
                    $userId = false;
                    $success = false;
                }
                $this->serviceKernel->setVariable($serviceId, 'userId', $userId);
                $this->serviceKernel->setVariable($serviceId, 'success', $success);

                if ($userId >= 1) {
                    $this->getLoginProfileInfo($userId, $serviceId);
                } else {
                    $this->serviceKernel->next(['serviceId' => $serviceId], true);
                }
            },
            function($response, $previousIndex) use ($serviceId, $password, $email) {
                if (!$response['data'][$previousIndex]) {
                    $profileId = false;
                    $onboarded = false;
                } else {
                    $profileResponse = $response['data'][$previousIndex][0];
                    $profileId = $profileResponse['id'];
                    $onboarded = $profileResponse['onboarded'];
                    $profileType = $profileResponse['type'];
                }

                $this->serviceKernel->setVariable($serviceId, 'profileId', $profileId);
                $this->serviceKernel->setVariable($serviceId, 'onboarded', $onboarded);
                $this->serviceKernel->setVariable($serviceId, 'profileType', $profileType);

                // TODO where to route valid user
                // verify password has most secure hash available
                $hashPass = $this->serviceKernel->getVariable($serviceId, 'hashPass');
                $userId = $this->serviceKernel->getVariable($serviceId, 'userId');
                if (password_needs_rehash($hashPass, PASSWORD_DEFAULT, $this->options) && $userId) {
                    $config = $this->generateConfigArray("updatePassword", 'update', $serviceId);
                    $config['data']['user']['email'] = $email;
                    $config['data']['user']['password'] = $password;
                    $config['data']['user']['reset_passcode'] = "invalid";

                    $this->send($config);
                } else {
                    $this->serviceKernel->next(['serviceId' => $serviceId], true);
                }
            },
            function ($response, $previousIndex) use ($serviceId) {
                $onboarded = $this->serviceKernel->getVariable($serviceId, 'onboarded');
                $userId = $this->serviceKernel->getVariable($serviceId, 'userId');
                $profileId = $this->serviceKernel->getVariable($serviceId, 'profileId');
                $success = $this->serviceKernel->getVariable($serviceId, 'success');
                $profileType = $this->serviceKernel->getVariable($serviceId, 'profileType');

                return ['data' => ["success" => $success, "userId" => $userId, "profileId" => $profileId, "profileType" => $profileType,'onboarded' => $onboarded]];
            }
        ], $serviceId);

        $this->serviceKernel->first($serviceId);
    }

    public function generateResetPassCode($email, $serviceId)
    {
        $resetCode = substr(str_shuffle(
            $email . 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'
        ), 0, 10);

        $resetPassCode = str_replace(array('@', '.'), '0', $resetCode);

        $expDate = new \DateTime('now');
        $expDate->modify('+1 day');
        $date = $expDate->format('Y-m-d H:i:s');

        $config = $this->generateConfigArray(__FUNCTION__, 'update', $serviceId);

        $config['data']['user']['email'] = $email;
        $config['data']['user']['reset_passcode'] = $resetPassCode;
        $config['data']['user']['reset_passcode_exp'] = $date;

        $this->serviceKernel->register([
            function() use($config, $serviceId) {
                $this->send($config);
            },
            function($response, $previousIndex) use($serviceId) {
                $result = array();
                $result['data'] = $response['data'][$previousIndex][0];
                return $result;
            }
        ], $serviceId);
        $this->serviceKernel->first($serviceId);
    }

    public function deleteResetPassCode($email)
    {
        $config = $this->generateConfigArray(__FUNCTION__, "update");

        $config['data']['user']['email'] = $email;
        $config['data']['user']['reset_passcode'] = null;
        $config['data']['user']['reset_passcode_exp'] = null;

        $response = $this->conn->sendToDB($config);

        if (!$response['success']) {
            return [];
        } else {
            return $response['data'][0];
        }
    }

    public function getEmailByResetCode($resetCode, $serviceId)
    {
        $config = $this->generateConfigArray(__FUNCTION__, 'select', $serviceId);

        $config['data']['user']['reset_passcode'] = $resetCode;

        $this->serviceKernel->register([
            function() use ($config, $serviceId) {
                $this->send($config);
            },
            function($response, $previousIndex) use($serviceId) {
                $result = array();
                $result['data'] = $response['data'][$previousIndex][0];
                return $result;
            }
        ], $serviceId);

        $this->serviceKernel->first($serviceId);
    }

    public function updatePassword($email, $password, $serviceId)
    {
        $config = $this->generateConfigArray(__FUNCTION__, 'update', $serviceId);
        $config['data']['user']['email'] = $email;
        $config['data']['user']['password'] = $password;
        $config['data']['user']['reset_passcode'] = "invalid";

        $this->serviceKernel->register([
            function() use ($config, $serviceId) {
                $this->send($config);
            },
            function($response, $previousIndex) use($serviceId) {
                $result = array();

                if ($response['data'][$previousIndex]) {
                    $password = $response['data'][$previousIndex]['password'];
                    if (is_string($password) && $password != null) {
                        $result['data']['password'] = $password;
                    } else {
                        $result['data']['password'] = null;
                    }
                }

                return $result;
            }
        ], $serviceId);

        $this->serviceKernel->first($serviceId);
    }

    public function getClientAws($serviceId)
    {
        $self = $this;
        $this->serviceKernel->register(
            [
                function() use ($self, $serviceId) {
                    $id = $this->awsConfig['client-aws']['id'];
                    $key = $this->awsConfig['client-aws']['key'];
                    $region = $this->agentConfig->get('client-aws.region');
                    $bucketName = $this->agentConfig->get('client-aws.bucket');

                    if (!isset($id, $key, $region, $bucketName)) {
                        throw new EDException('Failed reading AWS information from configurations', 20);
                    }

                    $response['data'] = [
                        'id' => $id,
                        'key' => $key,
                        'region' => $region,
                        'bucketName' => $bucketName,
                    ];

                    return $response;
                }
            ],
            $serviceId
        );

        $this->serviceKernel->first($serviceId);
    }

    /**
     * @param $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }
}
