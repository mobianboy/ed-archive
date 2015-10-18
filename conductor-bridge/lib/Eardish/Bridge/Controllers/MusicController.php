<?php
namespace Eardish\Bridge\Controllers;

use Eardish\Bridge\Agents\MusicAgent;
use Eardish\Bridge\Agents\RecommendationAgent;
use Eardish\Bridge\Agents\ProfileAgent;
use Eardish\Bridge\Controllers\Core\AbstractController;

class MusicController extends AbstractController
{
    protected $musicAgent;
    protected $recAgent;
    protected $profileAgent;
    protected $requestId;

    public function __construct(MusicAgent $musicAgent, RecommendationAgent $recAgent, ProfileAgent $profileAgent)
    {
        $this->musicAgent = $musicAgent;
        $this->recAgent = $recAgent;
        $this->profileAgent = $profileAgent;
    }

    /**
     * get an initial set of tracks to rate -- all genres
     *
     * @return array
     */
    public function getFullGenreBlendSet($requestId)
    {
        $this->kernel->setRequests([
            function() use ($requestId) {
                if (isset($this->dataBlock['count'])) {
                    $trackLimit = $this->dataBlock['count'];
                } else {
                    $trackLimit = 10;
                }

                $profileId = $this->metaBlock->getCurrentProfile();

                $this->kernel->setVariable($requestId, 'profileId', $profileId);
                $this->kernel->setVariable($requestId, 'trackLimit', $trackLimit);

                $this->recAgent->getFullGenreBlendSet($profileId, $trackLimit, $requestId);
            },
            function($response, $previousIndex) use ($requestId) {
                if(!$response['data'][$previousIndex]) {
                    $this->reportError('could not return tracks');
                }

                foreach ($response['data'][$previousIndex] as $trackEntry) {
                    $this->data[] = $trackEntry['track_id'];
                }

                $this->listType = "tracks";

                return $this->reportSuccess();
            }
        ],
            $requestId
        );
        $this->kernel->first($requestId);
    }


    /**
     * get a set of tracks from a single genre
     *
     * @return array
     */
    public function getSingleGenreBlendSet($requestId)
    {
        $this->kernel->setRequests([
            function() use($requestId) {
                if (isset($this->dataBlock['count'])) {
                    $trackLimit = $this->dataBlock['count'];
                } else {
                    $trackLimit = 10;
                }

                $profileId = $this->dataBlock['id'];
                $genreIds = $this->dataBlock['genreId'];

                $this->recAgent->getSingleGenreBlendSet($profileId, $genreIds, $trackLimit, $requestId);
            },
            function($response, $previousIndex) use($requestId) {
                if (!$response) {
                    $this->reportError('could not return tracks');
                }

                foreach ($response['data'][$previousIndex] as $trackEntry) {
                    $this->data[] = $trackEntry['track_id'];
                }

                $this->listType = "tracks";

                return $this->reportSuccess();
            }
        ], $requestId
        );
        $this->kernel->first($requestId);
    }

    /**
     * AR tools add just original Track/ audio, no transcoding
     * @return array
     */
    public function addTrack($requestId)
    {
        // upload original track media to S3
        // (should be able to generate and pass a constructed S3 URL by formula
        // even before upload
        $this->checkAccess($this->dataBlock['profileId']);

        $trackName          = $this->dataBlock['trackName'];
        $artistProfileId    = $this->dataBlock['profileId'];
        $trackArtId         = $this->dataBlock['artId'];

        $this->kernel->setRequests([
            function() use ($trackName, $artistProfileId, $trackArtId, $requestId){
                $this->musicAgent->addTrack($artistProfileId, $trackName, $trackArtId, $requestId); //track response
            },
            function($response, $previousIndex) use ($requestId) {
                $trackResponse = $response['data'][$previousIndex];
                if (!$trackResponse) {
                    $this->reportError('could not save track');
                }
                $this->data = $trackResponse;
                $trackId = intval($trackResponse['id']);
                $genreId = $this->dataBlock['genreId'];
                $this->musicAgent->addTrackGenre($trackId, $genreId, $requestId); //track genre response
            },
            function($response, $previousIndex) use ($requestId) {
                $trackGenreResponse = $response['data'][$previousIndex];
                if(!$trackGenreResponse['id']) {
                    $this->reportError('could not set track genre');
                }

                return $this->reportSuccess();
            }
        ], $requestId);

        $this->kernel->first($requestId);
    }

    public function addTrackUrl($requestId)
    {
        $profileId = $this->dataBlock['profileId'];
        $this->checkAccess($profileId);

        $this->kernel->setRequests([
            function() use ($requestId) {
                $trackUrl = $this->dataBlock['trackUrl'];
                $trackId = $this->dataBlock['trackId'];
                $format = "original";
                $this->musicAgent->attachAudioS3URL($trackId, $trackUrl, $format, $requestId); //audio response
            },
            function($response, $previousIndex) use($profileId, $requestId) {
                $audioResponse = $response['data'][$previousIndex];

                $this->data = array(
                    "profile_id" => $profileId,
                    "track_url" => $audioResponse['url'],
                    "audio_id" => intval($audioResponse['id'])
                );

                return $this->reportSuccess();
            }
        ], $requestId);

        $this->kernel->first($requestId);
    }

    public function softDeleteTrack($requestId)
    {
        $trackId = $this->dataBlock['trackId'];

        $this->kernel->setRequests([
            function() use($trackId, $requestId) {
                $this->musicAgent->softDeleteTrack($trackId, $requestId);
            },
            function($response, $previousIndex) use($requestId) {
                $result = $response['data'][$previousIndex];

                if (!$result['deleted']) {
                    $this->reportError('could not soft delete track');
                }

                return $this->reportSuccess();
            }
        ], $requestId
        );
        $this->kernel->first($requestId);

    }

    public function getCFURL($requestId)
    {
        $this->kernel->setRequests(
            [
                function() use ($requestId) {
                    $trackId = $this->dataBlock['id'];
                    $format = $this->dataBlock['format'];

                    $this->musicAgent->getCFurl($trackId, $format, $requestId);
                },
                function($response, $previousIndex) use ($requestId) {
                    if (count($response) === 0) {
                        $this->reportError('could not return CloudFront url');
                    }

                    $this->data = $response['data'][$previousIndex][0];
                    $this->data['url'] = $response['data'][$previousIndex][0]['relative_url'];

                    return $this->reportSuccess();
                }
            ],
            $requestId
        );

        $this->kernel->first($requestId);
    }

    /**
     * given a trackID, returns:
     * .. track name
     * .. artist name
     * .. track art location
     * .. song url location
     *
     * @return array
     */
    public function getTrackDetail($requestId)
    {
        $self = $this;

        $this->kernel->setRequests([
            // get the track detail
            function() use ($self, $requestId) {
                $profileId = $self->metaBlock->getCurrentProfile();
                $this->kernel->setVariable($requestId, 'profileId', $profileId);
                if (is_null($profileId)) {
                    $this->reportError('profile id not set');
                }

                $trackId = $self->dataBlock['id'];
                $this->musicAgent->getTrackDetail($trackId, $requestId);
            },
            // get the track rating
            function($response, $previousIndex) use ($self, $requestId) {
                if (!$response['data'][$previousIndex]) {
                    $this->reportError('could not return track detail');
                }
                $this->kernel->setVariable($requestId, 'artId', $response['data'][$previousIndex][0]['art_id']);
                $profileId = $this->kernel->getVariable($requestId, 'profileId');
                $trackId = $this->dataBlock['id'];
                $this->data = $response['data'][$previousIndex][0];
                $this->musicAgent->getProfileTrackRating($trackId, intval($profileId), $requestId);
            },
            // get the art urls
            function($response, $previousIndex) use ($self, $requestId) {
                if (count($response['data'][$previousIndex]) === 0) {
                    $rating = -1;
                } else {
                    $rating = $response['data'][$previousIndex]['rating'];
                }
                if (isset($response['data'][$previousIndex]) && is_array($response['data'][$previousIndex])) {
                    $this->data = array_merge($this->data, $response['data'][$previousIndex]);
                }
                $this->data['rating'] = $rating;
                $artId = $this->kernel->getVariable($requestId, 'artId');
                $this->profileAgent->getArtUrls($artId, $requestId);
            },
            // build the art map and return
            function($response, $previousIndex) use ($self, $requestId) {

                if (!$response['data'][$previousIndex]) {
                    $this->reportError('could not return art Urls');
                }
                $this->createArtMap($response['data'][$previousIndex]);
                $this->modelType = "media-track";

                return $this->reportSuccess();
            }
        ], $requestId);

        $this->kernel->first($requestId);
    }

    /**
     * rate a track (requires trackId and a rating)
     *
     * @return array
     */
    public function rateTrack($requestId)
    {
        $this->kernel->setRequests([
            function () use ($requestId) {
                $this->checkAccess($this->dataBlock['profileId']);

                $trackId = $this->dataBlock['trackId'];
                $profileId = $this->dataBlock['profileId'];
                $rating = $this->dataBlock['rating'];

                $this->kernel->setVariable($requestId, 'profileId', $profileId);
                $this->kernel->setVariable($requestId, 'trackId', $trackId);
                $this->kernel->setVariable($requestId, 'rating', $rating);

                $this->musicAgent->getProfileTrackRating($trackId, $profileId, $requestId);
            },
            function($response, $previousIndex) use ($requestId) {
                // if no entry for track rating, create rating
                if (!$response['data'][$previousIndex]) {
                    $profileId = $this->kernel->getVariable($requestId, 'profileId');
                    $trackId = $this->kernel->getVariable($requestId, 'trackId');
                    $rating = $this->kernel->getVariable($requestId, 'rating');

                    $this->musicAgent->rateTrack($profileId, $trackId, $rating, $requestId);
                } else {
                    $this->kernel->next(array('requestId' => $requestId), true);
                }

            },
            function($response, $previousIndex) use ($requestId) {
                if ($response['data'][$previousIndex -1]) {
                    $profileId = $this->kernel->getVariable($requestId, 'profileId');
                    $trackId = $this->kernel->getVariable($requestId, 'trackId');
                    $rating = $this->kernel->getVariable($requestId, 'rating');

                    $this->musicAgent->updateTrackRating($profileId, $trackId, $rating, $requestId);
                } else {
                    $this->kernel->next(array('requestId' => $requestId), true);
                }
            },
            function($response, $previousIndex) use ($requestId) {
                return $this->reportSuccess();
            }
        ],
            $requestId
        );
        $this->kernel->first($requestId);
    }

    public function updateTrack($requestId)
    {
        $this->kernel->setRequests([
            function() use ($requestId) {
                $trackData = $this->getClientTrackData($this->dataBlock);
                $trackData['data']['track']['id'] = $this->dataBlock['trackId'];
                $this->musicAgent->updateTrack($trackData['data']['track'], $requestId);
            },
            function($response, $previousIndex) {
                if (!$response['data'][$previousIndex] || !$response['data'][$previousIndex]['id']) {
                    $this->reportError('could not update track');
                }

                return $this->reportSuccess();
            }
        ], $requestId);

        $this->kernel->first($requestId);
    }

    public function listGenres($requestId)
    {
        $this->kernel->setRequests([
            function() use ($requestId) {
                $this->musicAgent->listGenres($requestId);
            },
            function($response, $previousIndex) {
                $genres = $response['data'][$previousIndex];
                if (!$genres) {
                    $this->reportError('failed retrieving genres');
                }
                $this->data = $genres;
                $this->listType = "genres";

                return $this->reportSuccess();
            }
        ], $requestId);

        $this->kernel->first($requestId);
    }
}
