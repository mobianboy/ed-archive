<?php
namespace Eardish\ProfileService;

use Monolog\Logger;
use Eardish\ProfileService\Core\AbstractService;

class ProfileService extends AbstractService
{
    /**
     * @var Logger
     */
    protected $logger;
    protected $config;

    public function getFullNameByEmail($email, $serviceId)
    {
        $config = $this->generateConfigArray(__FUNCTION__, 'select', $serviceId);
        $config['data']['user']['email'] = $email;

        $this->serviceKernel->register([
            function() use($config, $serviceId) {
                $this->send($config);
            },
            function($response, $previousIndex) use($serviceId) {
                $result = array();
                $result['data'] = $response['data'][$previousIndex][0];

                $firstName = $response['data'][$previousIndex][0]['first_name'];
                $lastName = $response['data'][$previousIndex][0]['last_name'];
                $result['data']['fullName'] = $firstName . " " . $lastName;

                return $result;
            }
        ], $serviceId);
        $this->serviceKernel->first($serviceId);
    }

    public function createProfile($profileData, $serviceId)
    {

        $config = $this->generateConfigArray(__FUNCTION__, 'insert', $serviceId);
        $config['data']['profile'] = $profileData;

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

    public function getArtUrls($artId, $serviceId) {

        $self = $this;
        $config = $self->generateConfigArray(__FUNCTION__, 'select', $serviceId);
        $config['data']['image']['art_id'] = $artId;

        $this->serviceKernel->register([
            function() use ($self, $config) {
                $this->send($config);
            },
            function($response, $previousIndex) use ($self) {

                $data = array();
                if ($response['data'][$previousIndex]) {
                    foreach ($response['data'][$previousIndex] as $val) {
                        $val['relative_url'] = $this->cf->makeStaticURL($val['relative_url'], [
                            'cf' => $this->agentConfig->get('profile.aws.cf')
                        ]);

                        $data['data'][] = $val;
                    }
                } else {
                    $data['data'] = [];
                }

                return $data;
            }
        ], $serviceId);

        $this->serviceKernel->first($serviceId);
    }

    public function selectContactInfo($contactId, $serviceId)
    {
        $config = $this->generateConfigArray(__FUNCTION__, 'select', $serviceId);
        $config['data']['contact']['id'] = $contactId;

        $this->serviceKernel->register([
            function() use ($config) {
                $this->send($config);
            },
            function($response, $previousIndex) {
                $result = array();
                $contactData = $response['data'][$previousIndex][0];
                if ($contactData) {
                    $result['data'] = $contactData;
                } else {
                    $result['data'] = [];
                }

                return $result;
            }
        ], $serviceId);

        $this->serviceKernel->first($serviceId);
    }

    public function createContactInfo($contactData, $serviceId)
    {
        $config = $this->generateConfigArray(__FUNCTION__, 'insert', $serviceId);
        $config['data']['contact'] = $contactData;

        $this->serviceKernel->register([
            function() use ($config) {
                $this->send($config);
            },
            function($response, $previousIndex) {
                $result = array();
                $contactId = $response['data'][$previousIndex][0]['id'];
                if ($contactId) {
                    $result['data']['contactId'] = $contactId;
                } else {
                    $result['data'] = [];
                }

                return $result;
            }
        ], $serviceId);

       $this->serviceKernel->first($serviceId);
    }

    public function editContactInfo($contactData, $serviceId)
    {

        $config = $this->generateConfigArray(__FUNCTION__, 'update', $serviceId);
        $config['data']['contact'] = $contactData;

        $this->serviceKernel->register([
            function() use ($config) {
                $this->send($config);
            },
            function($response, $previousIndex) {
                $result = array();
                $data = $response['data'][$previousIndex][0];
                if ($response) {
                    $result['data'] = $data;
                }

                return $result;
            }
        ], $serviceId);

        $this->serviceKernel->first($serviceId);
    }

    public function getArtistContent($profileId, $serviceId)
    {
        $config = $this->generateConfigArray(__FUNCTION__, 'select', $serviceId);
        $config['data']['profile']['id'] = $profileId;

        $this->serviceKernel->register([
            function () use ($serviceId, $config) {
                $this->send($config);
            },
            function ($response, $previousIndex) use ($serviceId) {
                $result = [];
                if (!$response['data'][$previousIndex]) {
                    $result['data'] = array();
                } else {
                    foreach ($response['data'][$previousIndex] as $val) {
                        $val['url'] = $this->cf->makeStaticURL($val['relative_url'], [
                            'cf' => $this->agentConfig->get('profile.aws.cf')
                        ]);
                        $val['track_url'] = $this->cf->makeStaticURL($val['relative_audio'], [
                            'cf' => $this->agentConfig->get('music.aws.cf')
                        ]);
                        $result['data'][] = $val;
                    }
                }
                return $result;
            }
        ], $serviceId);

        $this->serviceKernel->first($serviceId);

    }

    public function getSenderByInviteCode($inviteCode)
    {
        $config = $this->generateConfigArray(__FUNCTION__, 'select');
        $config['data']['invite']['invite_code'] = $inviteCode;

        $response = $this->conn->sendToDB($config);

        return $response;
    }

    public function selectProfile($profileId, $serviceId)
    {
        $config = $this->generateConfigArray(__FUNCTION__, 'select', $serviceId);
        $config['data']['profile']['id'] = $profileId;

        $this->serviceKernel->register([
            function() use ($config) {
                $this->send($config);
            },
            function ($response, $previousIndex) {
                $result = array();
                if (!$response['data'][$previousIndex]) {
                    $result['data'] = array();
                } else {
                    $result['data'] = $response['data'][$previousIndex];
                }

                return $result;
            }

        ], $serviceId);

        $this->serviceKernel->first($serviceId);
    }

    public function updateProfileIsOnboarded($profileId, $serviceId)
    {
        $config = $this->generateConfigArray(__FUNCTION__, 'update', $serviceId);
        $config['data']['profile']['id'] = $profileId;
        $config['data']['profile']['onboarded'] = true;

        $this->serviceKernel->register([
            function() use ($config) {
                $this->send($config);
            },
            function($response, $previousIndex) {
                if ($response['data'][$previousIndex]) {
                    return ['data' => true];
                }
                return ['data' => false];
            }
        ], $serviceId);

        $this->serviceKernel->first($serviceId);
    }

    public function editArtistProfile($profileData, $serviceId)
    {
        $self = $this;

        $this->serviceKernel->register([
            function () use ($self, $serviceId, $profileData) {
                $config = $this->generateConfigArray("editArtistProfile", 'update', $serviceId);
                $config['data']['profile'] = $profileData;
                $this->send($config);
            },
            function ($response, $previousIndex) use ($self, $serviceId) {
                $result = array();
                if (!$response['data'][$previousIndex]) {
                    $result['data'] = array();
                } else {
                    $result['data']  = $response['data'][$previousIndex][0];
                }

                return $result;
            }
        ], $serviceId);

        $this->serviceKernel->first($serviceId);
    }
    public function deleteArtistProfile($profileId)
    {
        $config = $this->generateConfigArray(__FUNCTION__, 'delete');

        $config['data']['profile']['profile_id'] = $profileId;

        $response['profile-deleted'] = $this->conn->sendToDB($config);

        return $response;
    }

    public function addArt($profileId, $artLoc)
    {
        $config = $this->generateConfigArray(__FUNCTION__, 'insert');

        $config['data']['art']['profile_id'] = $profileId;
        $config['data']['art']['art_loc'] = $artLoc;

        $response['art-added'] = $this->conn->sendToDB($config);

        return $response;
    }

    public function deleteArt($profileId, $artId)
    {
        $config = $this->generateConfigArray(__FUNCTION__, 'delete');

        $config['data']['art']['profile_id'] = $profileId;
        $config['data']['art']['art_id'] = $artId;

        $response['art-deleted'] = $this->conn->sendToDB($config);

        return $response;
    }

    public function listArtistProfiles($serviceId)
    {
        $config = $this->generateConfigArray(__FUNCTION__, 'select', $serviceId);

        $this->serviceKernel->register([
            function() use ($config, $serviceId) {
                 $this->send($config);
            },
            function($response, $previousIndex) use ($serviceId) {
                $result = array();
                $result['data'] = $response['data'][$previousIndex];

                if (!$result) {
                    $result['data'] = array();
                }

                return $result;
            }
        ], $serviceId);

        $this->serviceKernel->first($serviceId);
    }
}
