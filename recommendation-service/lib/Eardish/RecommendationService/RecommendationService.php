<?php
namespace Eardish\RecommendationService;

use Eardish\RecommendationService\Core\AbstractService;
use Monolog\Logger;

class RecommendationService extends AbstractService
{
    /**
     * @var Logger
     */
    protected $logger;
    protected $port = "9000";
    protected $addr = "localhost";

    public function modifyProfileGenreBlend($profileId, array $genreIds, array $weights, $serviceId)
    {
        $this->serviceKernel->register([
            function() use ($serviceId, $profileId) {
                // delete the genre prefs for the profile
                $deleteGenrePrefs = $this->generateConfigArray("wipeProfileGenreBlend", "delete", $serviceId);
                $deleteGenrePrefs['data']['profile_genre_blend']['profile_id'] = $profileId;
                $this->send($deleteGenrePrefs);
            },
            function($response, $previousIndex) use ($serviceId, $genreIds, $weights, $profileId) {
                // insert genre prefs for profile
                $columnDatas = array();
                $count = count($genreIds);
                for ($i = 0; $i < $count; $i++) {
                    $columnDatas[] = ["profile_id" => $profileId, "genre_id" => $genreIds[$i], "weight" => $weights[$i]];
                }

                $dbRequest = $this->generateConfigArray("createProfileGenreBlend", "insert", $serviceId);
                $dbRequest['multi'] = true;
                $dbRequest['data']['profile_genre_blend'] = $columnDatas;

                $this->send($dbRequest);
            },
            function($response, $previousIndex) {
                $sorted = $this->sortResults($response['data'][$previousIndex]);

                return ['data' => $sorted];
            }
        ], $serviceId);

        $this->serviceKernel->first($serviceId);
    }

    public function getProfileGenreBlend($profileId, $serviceId)
    {
        $self = $this;

        $this->serviceKernel->register([
            function () use ($self, $serviceId, $profileId) {
                $dbRequest = $this->generateConfigArray("getProfileGenreBlend", "select", $serviceId);
                $dbRequest['data']['profile_genre_blend']['profile_id'] = $profileId;
                $this->send($dbRequest);
            },
            function ($response, $previousIndex) use ($self, $serviceId) {
                if (!count($response['data'][$previousIndex])) {
                    return ['data' => []];
                }
                $sorted = $this->sortResults($response['data'][$previousIndex]);

                return ['data' => $sorted];
            }
        ], $serviceId);

        $this->serviceKernel->first($serviceId);
    }

    public function getSingleGenreBlendSet($profileId, $genreId, $limit, $serviceId)
    {
        $config = $this->generateConfigArray(__FUNCTION__, 'select', $serviceId);

        $config['data']['track_genre']['genre_id'] = $genreId;
        $config['data']['track_genre']['profile_id'] = $profileId;
        $config['params']['limit'] = $limit;

        $this->serviceKernel->register([
            function() use ($config, $serviceId) {
                $this->send($config);
            },
            function($response, $previousIndex) use ($serviceId) {
                $result = array();
                $result['data'] = $response['data'][$previousIndex];

                return $result;
            }
        ], $serviceId);

        $this->serviceKernel->first($serviceId);
    }

    public function getFullGenreBlendSet($profileId, $limit, $serviceId)
    {
        $config = $this->generateConfigArray(__FUNCTION__, 'select', $serviceId);

        $config['data']['profile_genre_blend']['profile_id'] = $profileId;
        $config['params']['limit'] = $limit;

        $this->serviceKernel->register([
            function() use ($config, $serviceId) {
                $this->send($config);

            },
            function($response, $previousIndex) use ($serviceId) {
                $result = array();
                $result['data'] = $response['data'][$previousIndex];

                return $result;

            }
        ], $serviceId);

        $this->serviceKernel->first($serviceId);
    }

    /**
     * @param $data
     * @return mixed
     *
     */
    private function sortResults($data)
    {
        $sorted['anti'] = [];
        $sorted['preferred'] = [];
        foreach ($data as $result) {
            if ($result['weight'] == 0) {
                $sorted['anti'][] = array(
                    'genreId' => $result['genre_id'],
                    'weight' => $result['weight']
                );
            } elseif ($result['weight'] == 2) {
                $sorted['preferred'][] = array(
                    'genreId' => $result['genre_id'],
                    'weight' => $result['weight']
                );
            }
        }

        return $sorted;
    }

    /**
     * @param $logger
     * @codeCoverageIgnore
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return Logger
     * @codeCoverageIgnore
     */
    public function getLogger()
    {
        return $this->logger;
    }
}