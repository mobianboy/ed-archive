<?php
namespace Eardish\UserService;

use Monolog\Logger;
use Eardish\UserService\Core\AbstractService;

class UserService extends AbstractService
{
    /**
     * @var Logger
     */
    protected $logger;

    public function createUser($email, $password, $serviceId)
    {
        $config = $this->generateConfigArray(__FUNCTION__, 'insert', $serviceId);

        $config['operation'] = 'insert';
        $config['data']['user']['email'] = $email;
        $config['data']['user']['password'] = $password;

        $this->serviceKernel->register([
            function () use ($config) {
                $this->send($config);
            },
            function ($response, $previousIndex) {
                $result = array();

                $userId = $response['data'][$previousIndex][0]['id'];
                if ($userId) {
                    $result['data']['userId'] = $userId;}

                return $result;
            }
        ], $serviceId);

        $this->serviceKernel->first($serviceId);
    }

    public function checkIfEmailAlreadyExists($email, $serviceId)
    {
        $config = $this->generateConfigArray(__FUNCTION__, 'select', $serviceId);
        $config['data']['user']['email'] = $email;
        $this->serviceKernel->register([
            function () use ($config) {
                $this->send($config);
            },
            function ($response, $previousIndex) {
                if ($response['data'][$previousIndex])
                    return ['data' => ['email-exists' => true]];
                else {
                    return ['data' => ['email-exists' => false]];
                }
            }
        ], $serviceId);

        $this->serviceKernel->first($serviceId);
    }

    public function createUserProfile($userId, $firstName, $lastName, $yearOfBirth, $zipcode)
    {
        $config = $this->generateConfigArray(__FUNCTION__, 'insert');

        $config['data']['profile']['user_id'] = $userId;
        $config['data']['profile']['first_name'] = $firstName;
        $config['data']['profile']['last_name'] = $lastName;
        $config['data']['profile']['year_of_birth'] = $yearOfBirth;
        $config['data']['profile']['zipcode'] = $zipcode;

        $response = $this->conn->sendToDB($config);

        $result = array();
        if ($response['success']) {
            $profileId = $response['data'][0]['id'];
            if ($profileId >= 1) {
                $result['profileID'] = $profileId;
            }
        } else {
            $result['profileID'] = 0;
        }

        return $result;
    }

    /**
     * @param $userId
     * @param $serviceId
     * @return array
     */
    public function invitesUsed($userId, $serviceId)
    {
        $config = $this->generateConfigArray(__FUNCTION__, 'select', $serviceId);

        $config['data']['invite']['inviter_id'] = $userId;

        $this->serviceKernel->register([
            function() use ($config){
                $this->send($config);
            }, function($response, $previousIndex) {
                if ($response['data'][$previousIndex][0]) {
                    return ["data" => ["invitesUsed" => $response['data'][$previousIndex][0]['count']]];
                }
                return ["data" => ["invitesUsed" => -1]];
            }
        ], $serviceId);

        $this->serviceKernel->first($serviceId);

    }

    public function extraInvites($userId, $serviceId)
    {
        $config = $this->generateConfigArray(__FUNCTION__, 'select', $serviceId);

        $config['data']['user']['id'] = $userId;

        $this->serviceKernel->register([
            function() use($config, $serviceId) {
                $this->send($config);
            },
            function($response, $previousIndex) use($serviceId) {
                $extraInvites = $response['data'][$previousIndex][0]['extra_invites'];

                if ($extraInvites == null) {
                    return ["data" => ["extraInvites" => 0]];
                }

                return ["data" => ["extraInvites" => $extraInvites]];
            }
        ], $serviceId
        );
        $this->serviceKernel->first($serviceId);
    }

    public function updateExtraInvites($userId, $invitesLeft, $serviceId)
    {
        $config = $this->generateConfigArray(__FUNCTION__, 'update', $serviceId);

        $config['data']['user']['id'] = $userId;
        $config['data']['user']['extra_invites'] = $invitesLeft;


        $this->serviceKernel->register([
            function() use($config, $serviceId) {
                $this->send($config);
            },
            function($response, $previousIndex) use($serviceId) {
                $result = array();
                if (!$response["data"][$previousIndex][0]) {
                    $result['data']['extraInvites'] = array();
                } else {
                    $result['data']['extraInvites'] = $response['data'][$previousIndex][0]['extra_invites'];
                }
                return $result;
            }
        ], $serviceId
        );
        $this->serviceKernel->first($serviceId);
    }

    public function registerInviteCode($userId, $inviteCode, $inviteeEmail, $serviceId)
    {
        $config = $this->generateConfigArray(__FUNCTION__, 'insert', $serviceId);

        $config['data']['invite']['inviter_id'] = $userId;
        $config['data']['invite']['invite_code'] = $inviteCode;
        $config['data']['invite']['invitee_email'] = $inviteeEmail;

        $this->serviceKernel->register([
            function() use ($config) {
                $this->send($config);
            },
            function ($response, $previousIndex) {
                $result = ["data" => ["registerInviteCode" => $response['data'][$previousIndex][0]]];
                return $result;
            }
        ], $serviceId);

        $this->serviceKernel->first($serviceId);
    }

    /**
     * @codeCoverageIgnore
     */
    public function createInviteCode($serviceId)
    {
        $this->serviceKernel->register([
            function(){
                $inviteCode = ["data" => ["inviteCode" => substr(md5(uniqid(mt_rand(), true)) , 0, 8)]];
                return $inviteCode;
            }
        ], $serviceId);

        $this->serviceKernel->first($serviceId);

    }

    public function validateInviteCode($inviteCode, $serviceId)
    {
        $config = $this->generateConfigArray(__FUNCTION__, 'select', $serviceId);

        $config['data']['invite']['invite_code'] = $inviteCode;

        $this->serviceKernel->register([
            function() use ($config) {
                $this->send($config);
            },
            function($response, $previousIndex) {
                $result = array();

                if (!$response['data'][$previousIndex] || !$response['data'][$previousIndex][0]['invite_code'] || !$response['data'][$previousIndex][0]['inviter_id']){
                    $result['data'] = array();
                } else {
                    $result['data'] = $response['data'][$previousIndex][0];
                }
                return $result;
            }
        ], $serviceId);

        $this->serviceKernel->first($serviceId);
    }

    public function redeemInviteCode($userId, $inviteId, $serviceId)
    {
        $config = $this->generateConfigArray(__FUNCTION__, 'update', $serviceId);

        $config['data']['invite']['invitee_id'] = $userId;
        $config['data']['invite']['id'] = $inviteId;

        $this->serviceKernel->register([
            function() use ($config) {
                $this->send($config);
            },
            function($response, $previousIndex) {
                $result = array();

                $result['data'] = $response['data'][$previousIndex][0];
                return $result;
            }
        ], $serviceId);

      $this->serviceKernel->first($serviceId);

    }

    /**
     * @codeCoverageIgnore
     * @param $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * @codeCoverageIgnore
     * @return Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }
}
