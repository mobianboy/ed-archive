<?php
namespace Eardish\AnalyticsService;

use Eardish\Exceptions\EDException;
use Eardish\AnalyticsService\Core\AbstractService;
use Monolog\Logger;

class AnalyticsService extends AbstractService
{
    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @param array $data
     * @param $serviceId
     * @return mixed
     */
    public function submitEntry(array $data, $serviceId)
    {

        $this->serviceKernel->register([
            function () use ($serviceId, $data) {
                $flatData = $this->recurseArray($data);

                // Set values for config array to prepare to send to DB
                $config = $this->generateConfigArray("submitEntry", 'insert', $serviceId);

                $config['data']['analytic']['device_type'] = $this->getValue($flatData, "common.device.type");
                $config['data']['analytic']['device_make'] = $this->getValue($flatData, "common.device.make");
                $config['data']['analytic']['device_model'] = $this->getValue($flatData, "common.device.model");
                $config['data']['analytic']['device_carrier'] = $this->getValue($flatData, "common.device.carrier");
                $config['data']['analytic']['device_os'] = $this->getValue($flatData, "common.device.OS");
                $config['data']['analytic']['device_uuid'] = $this->getValue($flatData, "common.device.UUID");
                $config['data']['analytic']['client_version'] = $this->getValue($flatData, "common.clientVersion");
                $config['data']['analytic']['latitude'] = $this->getValue($flatData, "common.location.lat");
                $config['data']['analytic']['longitude'] = $this->getValue($flatData, "common.location.long");
                $config['data']['analytic']['time'] = $this->getValue($flatData, "common.time");
                $config['data']['analytic']['user_id'] = $this->getValue($flatData, "common.user");
                $config['data']['analytic']['profile_id'] = $this->getValue($flatData, "common.profile");
                $config['data']['analytic']['view_route'] = $this->getValue($flatData, "common.viewRoute");
                $config['data']['analytic']['session_duration'] = $this->getValue($flatData, "common.session.duration");
                $config['data']['analytic']['event_type'] = $this->getValue($flatData, "event.type");


                $config['data']['analytic']['values'] = json_encode($data['event']['values']);

                foreach ($data['common']['viewState'] as $state => $values) {
                    $state = $this->camelToSnake($state);
                    $config['data']['analytic'][$state] = json_encode($values);
                }
                $trackEvent = 0;
                foreach ($data['event']['values'] as $key => $value) {
                    if ($key == "trackId") {
                        $dbConfig['data']['analytic']['track_id'] = $value;
                        $config['data']['analytic']['track_id'] = $value;
                        //get artist profile id for a track to make lookups easier for charts
                        $dbConfig = $this->generateConfigArray("getArtistIdFromTrackId", 'select', $serviceId);
                        $dbConfig['data']['track']['id'] = $value;
                        $this->send($dbConfig);
                        $trackEvent++;
                    }
                    if ($key == "timecode") {
                        $config['data']['analytic']['track_timecode'] = $value;
                    }
                }
                $this->serviceKernel->setVariable($serviceId, "config", $config);
                if ($trackEvent == 0) {
                    $this->send($config);
                }
            },
            function ($response, $previousIndex) use ($serviceId) {
                $config = $this->serviceKernel->getVariable($serviceId, "config");
                if ($response['data'][$previousIndex][0]['profile_id']) {
                    $config['data']['analytic']['artist_id'] = $response['data'][$previousIndex][0]['profile_id'];
                    $this->send($config);
                } else {
                    $this->serviceKernel->selfNext(["serviceId" => $serviceId]);
                }
            },
            function ($response, $previousIndex) use ($serviceId) {
                $config = $this->serviceKernel->getVariable($serviceId, "config");
                if ($config['data']['analytic']['event_type'] == 'play') {
                    $trackConfig = $this->generateConfigArray("insertTrackPlay", 'insert', $serviceId);
                    $trackConfig['data']['track_play']['track_id'] = $config['data']['analytic']['track_id'];
                    $trackConfig['data']['track_play']['profile_id'] = $config['data']['analytic']['profile_id'];

                    $this->send($trackConfig);
                } else {
                    $this->serviceKernel->selfNext(["data" => "", "serviceId" => $serviceId]);
                }
            },
            function ($response, $previousIndex) use ($serviceId) {
                return ['data' => [
                    "success" => true
                ]];
            }
        ], $serviceId);

        $this->serviceKernel->first($serviceId);
    }

    private function camelToSnake($word, $splitter = "_")
    {
        $camel = preg_replace('/(?!^)[[:upper:]][[:lower:]]/', '$0', preg_replace('/(?!^)[[:upper:]]+/', $splitter.'$0', $word));

        return strtolower($camel);
    }

    private function getValue($data, $key)
    {
        if (isset($data[$key])) {
            return $data[$key];
        }

        return null;
    }

    private function recurseArray($array, $builtKey = "")
    {
        $values = array();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                if (!empty($builtKey)) {
                    $values = array_merge($values, $this->recurseArray($value, $builtKey.".".$key));
                } else {
                    $values = array_merge($values, $this->recurseArray($value, $key));
                }
            } else {
                if (!empty($builtKey)) {
                    $values[$builtKey.".".$key] = $value;
                } else {
                    $values[$key] = $value;
                }
            }
        }
        return $values;
    }

    //________STATS__________

    public function getCompletedListens($userId, $start, $end, $event, $serviceId)
    {
        $config = $this->generateConfigArray(__FUNCTION__, 'select', $serviceId);
        $config['data']['analytic']['event_type'] = $event;
        $config['data']['analytic']['user_id'] = $userId;
        $config['params']['start'] = $start;
        $config['params']['end'] = $end;
        $self = $this;

        $this->serviceKernel->register([
            function () use ($config, $serviceId) {
                $this->send($config);
            },
            function ($response, $previousIndex) use ($self) {
                $result = [];
                $result['data'] = $response['data'][$previousIndex];
                if (!array_key_exists('0', $response['data'][$previousIndex])) {
                    return ['data' => ['count' => 0]];
                }
                $result['data'] = ['count' => $response['data'][$previousIndex][0]['count']];

                return $result;
            }
        ], $serviceId);

        $this->serviceKernel->first($serviceId);
    }

    public function getRatedTracks($profileId, $start, $stop, $serviceId)
    {
        $config = $this->generateConfigArray(__FUNCTION__, 'select', $serviceId);

        $config['params']['start'] = $start;
        $config['params']['stop'] = $stop;
        $config['data']['track_rating']['profile_id'] = $profileId;
        $self = $this;

        $this->serviceKernel->register([
            function () use ($config, $serviceId) {
                $this->send($config);
            },
            function ($response, $previousIndex) use ($self, $serviceId) {
                $result = [];
                $result['data'] = $response['data'][$previousIndex];
                if (!array_key_exists('0', $response['data'][$previousIndex])) {
                    return ['data' => ['count' => 0]];
                }
                $result['data'] = ['count' => $response['data'][$previousIndex][0]['count']];

                return $result;
            }
        ], $serviceId);

        $this->serviceKernel->first($serviceId);

    }
    //________CHARTS__________
    public function getCompletedListensChart($start, $stop, $groupBy, $serviceId)
    {
        $config = $this->generateConfigArray(__FUNCTION__, 'select', $serviceId);

        $config['data']['analytic']['event_type'] = "completedListen";
        $config['params']['start'] = $start;
        $config['params']['stop'] = $stop;
        $config['params']['group_by'] = $groupBy;

        $this->serviceKernel->register([
            function() use ($config) {
                $this->send($config);
            },
            function ($response, $previousIndex) {
                $result = array();
                $result['data'] = $response['data'][$previousIndex];

                return $result;
            }
        ], $serviceId);

        $this->serviceKernel->first($serviceId);
    }

    public function getHighestRatedChart($start, $stop, $groupBy, $serviceId)
    {
        $config = $this->generateConfigArray(__FUNCTION__, 'select', $serviceId);

        $config['data']['analytic']['event_type'] = "rate";
        $config['params']['start'] = $start;
        $config['params']['stop'] = $stop;
        $config['params']['group_by'] = $groupBy;

        $this->serviceKernel->register([
            function() use ($config) {
                $this->send($config);
            },
            function ($response, $previousIndex) {
                $result = array();
                $result['data'] = $response['data'][$previousIndex];

                return $result;
            }
        ], $serviceId);

        $this->serviceKernel->first($serviceId);
    }

    public function getCompletedListensFans($start, $stop, $serviceId)
    {
        $config = $this->generateConfigArray(__FUNCTION__, 'select', $serviceId);

        $config['data']['analytic']['event_type'] = "completedListen";
        $config['params']['start'] = $start;
        $config['params']['stop'] = $stop;

        $this->serviceKernel->register([
            function() use ($config) {
                $this->send($config);
            },
            function ($response, $previousIndex) {
                $result = array();
                $result['data'] = $response['data'][$previousIndex];

                return $result;
            }
        ], $serviceId);

        $this->serviceKernel->first($serviceId);
    }

    public function getMostTracksRatedFans($start, $stop, $serviceId)
    {
        $config = $this->generateConfigArray(__FUNCTION__, 'select', $serviceId);

        $config['params']['start'] = $start;
        $config['params']['stop'] = $stop;

        $this->serviceKernel->register([
            function() use ($config) {
                $this->send($config);
            },
            function ($response, $previousIndex) {
                $result = array();
                $result['data'] = $response['data'][$previousIndex];

                return $result;
            }
        ], $serviceId);

        $this->serviceKernel->first($serviceId);
    }

    //_________Badges_________
    public function distributeBadges($badgeWinners, $serviceId)
    {
        $tempIds = [];
        foreach ($badgeWinners as $index => $winner) {
            if (array_key_exists("track_id", $winner)) {
                $tempIds[$winner['badge_id']][] = $winner['track_id'];
                unset($badgeWinners[$index]);
            }
        }
        $this->serviceKernel->register([
            function() use ($tempIds, $serviceId){
                $config = $this->generateConfigArray("getArtistIdsFromMultipleTracks", 'select', $serviceId);
                $config['data']['track']['ids'] = $tempIds[3];

                $this->send($config);
            },
            function($response, $previousIndex) use ($tempIds, $serviceId){
                $responseData = $response['data'][$previousIndex];
                if (!$responseData) {
                    return ['data' => false];
                }
                unset($responseData['success']);
                $updatedBadgeWinners = [];
                foreach ($responseData as $updatedResponse) {
                    $updatedBadgeWinners[] = [
                        "badge_id" => 3,
                        "profile_id" => $updatedResponse['profile_id']
                    ];
                }

                $this->serviceKernel->setVariable($serviceId, "updatedBadgeWinners", $updatedBadgeWinners);
                $config = $this->generateConfigArray("getArtistIdsFromMultipleTracks", 'select', $serviceId);
                $config['data']['track']['ids'] = $tempIds[4];

                $this->send($config);
            },
            function($response, $previousIndex) use ($badgeWinners, $serviceId){
                $responseData = $response['data'][$previousIndex];
                if (!$responseData) {
                    return ['data' => false];
                }
                unset($responseData['success']);
                $updatedBadgeWinners = $this->serviceKernel->getVariable($serviceId, "updatedBadgeWinners");
                foreach ($responseData as $updatedResponse) {
                    $updatedBadgeWinners[] = [
                        "badge_id" => 4,
                        "profile_id" => $updatedResponse['profile_id']
                    ];
                }

                $badgeWinners = array_merge($badgeWinners, $updatedBadgeWinners);
                $config = $this->generateConfigArray("distributeBadges", 'insert', $serviceId);
                $config['multi'] = true;
                $config['data']['profile_badge'] = $badgeWinners;
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

    public function getBadges($serviceId)
    {
        $config = $this->generateConfigArray(__FUNCTION__, 'select', $serviceId);

        $this->serviceKernel->register([
            function() use ($config) {
                $this->send($config);
            },
            function($response, $previousIndex) {
                $result = array();
                $data = $response['data'][$previousIndex];

                foreach ($data as $badge) {
                    $badges[$badge['description']] = $badge;
                }

                if ($response) {
                    $result['data'] = $badges;
                }
                return $result;
            }
        ], $serviceId);

        $this->serviceKernel->first($serviceId);
    }

    public function getProfileBadges($profileId, $serviceId)
    {
        $config = $this->generateConfigArray(__FUNCTION__, 'select', $serviceId);
        $config['data']['profile_badge']['profile_id'] = $profileId;

        $this->serviceKernel->register([
            function() use ($config) {
                $this->send($config);
            },
            function($response, $previousIndex) {
                $result = array();
                $data = $response['data'][$previousIndex];
                if ($response) {
                    $result['data'] = $data;
                }
                return $result;
            }
        ], $serviceId);

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
