<?php
namespace Eardish\MusicService;

use Eardish\AWS\CloudFront\CFUtils;
use Eardish\MusicService\Core\AbstractService;
use Monolog\Logger;

class MusicService extends AbstractService
{
    /**
     * @var Logger
     */
    protected $logger;
    /*
     * Database Port number
     */
    protected $port;

    /*
     * Database host location
     */
    protected $addr;

    /*
     * S3 instance
     */
   // protected $s3;

    /*
     * track bucket
     */
    protected $bucket   = "eardish.dev.songs";

    /**
     * region
     */
    protected $region   = "us-west-2";


    protected $originId = "S3-eardish.dev.songs/test";

    protected function getCFMode()
    {
        return [
            'region' => $this->agentConfig->get('music.aws.region'),
            'bucket' => $this->agentConfig->get('music.aws.bucket'),
            'cf' => $this->agentConfig->get('music.aws.cf')
        ];
    }


    public function addTrack($artistProfileId, $trackName, $trackArtId, $serviceId)
    {
        $config = $this->generateConfigArray(__FUNCTION__, 'insert', $serviceId);
        $config['data']['track']['profile_id']  = $artistProfileId;
        $config['data']['track']['name']        = $trackName;
        $config['data']['track']['art_id']      = $trackArtId;

        $this->serviceKernel->register([
            function() use ($config) {
                $this->send($config);
            },
            function($response, $previousIndex) {
                $result = array();
                $result['data'] = $response[$previousIndex][0];
                return $result;
            }
        ], $serviceId);

        $this->serviceKernel->first($serviceId);

    }


    public function createAlbum($profileId, $albumName, $serviceId)
    {
        $config = $this->generateConfigArray(__FUNCTION__, 'insert', $serviceId);
        $config['data']['album']['profile_id'] = $profileId;
        $config['data']['album']['name'] = $albumName;

        $this->serviceKernel->register([
            function() use ($config) {
                $this->send($config);

            },
            function($response, $previousIndex) {
                $result = array();
                $result['data'] = $response[$previousIndex][0];

                return $result;
            }
        ], $serviceId);

        $this->serviceKernel->first($serviceId);
    }


    // BELOW SHOULD MOVE TO TRANSCODER SERVICE
    /**
     * renders all required files
     * gets original file from S3 - puts into local FS
     * calls transcoder for conversion
     * pushes files to S3
     * deletes files from filesystem (including original)
     * records new URLs for the track in the DB
     *
     * @param $trackId
     * @param $profileId
     */
    public function renderFullAudioFileSet($trackId, $profileId)
    {
        $config = $this->generateConfigArray(__FUNCTION__, 'select');
        $config['data']['track']['track_id']    = $trackId;
      //  $config['data']['track']['profile_id']  = $profileId;

        $originalAF = $this->conn->send($config);

        //TODO - GET THE FILE FROM S3 AND STORE IT LOCAL TO THE TRANSCODER DAEMON
    }

    public function updateTrack($trackData, $serviceId)
    {
        $config = $this->generateConfigArray(__FUNCTION__, 'update', $serviceId);
        $config['data']['track'] = $trackData;

        $this->serviceKernel->register([
            function() use ($config) {
                $this->send($config);
            },
            function($response, $previousIndex) {
                $result = array();
                $result['data'] = $response[$previousIndex][0];

                return $result;
            }
        ], $serviceId);

        $this->serviceKernel->first($serviceId);
    }

    public function updateTrackRating($profileId, $trackId, $rating, $serviceId)
    {
        $config = $this->generateConfigArray(__FUNCTION__, 'update', $serviceId);

        $config['data']['track_rating']['profile_id'] = $profileId;
        $config['data']['track_rating']['track_id'] = $trackId;
        $config['data']['track_rating']['rating'] = $rating;

        $this->serviceKernel->register([
            function () use ($config, $serviceId) {

                $this->send($config);
            },
            function($response, $previousIndex) {
                $result = array();
                $result['data'] = $response[$previousIndex][0];

                return $result;
            }
        ], $serviceId);

        $this->serviceKernel->first($serviceId);
    }

    public function rateTrack($profileId, $trackId, $rating, $serviceId)
    {
        $config = $this->generateConfigArray(__FUNCTION__, 'insert', $serviceId);

        $config['data']['track_rating']['track_id']     = $trackId;
        $config['data']['track_rating']['profile_id']   = $profileId;
        $config['data']['track_rating']['rating']       = $rating;

        $this->serviceKernel->register([
            function () use ($config, $serviceId) {
                $this->send($config);
            },
            function($response, $previousIndex) {
                $result = array();
                $result['data'] = $response[$previousIndex][0];

                return $result;
            }
        ], $serviceId);

        $this->serviceKernel->first($serviceId);
    }

    public function getTrackDetail($trackId, $serviceId)
    {
        $config = $this->generateConfigArray(__FUNCTION__, 'select', $serviceId);
        $config['data']['track']['id'] = $trackId;
        $self = $this;

        $this->serviceKernel->register([
            function() use ($self, $config) {
                $this->send($config);
            },
            function($response, $oldIndex) use ($self) {
                $result = array();
                $result['data'] = $response[$oldIndex];

                return $result;
            }
        ], $serviceId);

        $this->serviceKernel->first($serviceId);
    }

    public function getProfileTrackRating($trackId, $profileId, $serviceId)
    {
        $config = $this->generateConfigArray(__FUNCTION__, 'select', $serviceId);
        $config['data']['track_rating']['track_id'] = $trackId;
        $config['data']['track_rating']['profile_id'] = $profileId;
        $self = $this;

        $this->serviceKernel->register([
            function() use ($self, $config) {
                $this->send($config);
            },
            function($response, $oldIndex) use ($self) {
                $result = array();
                if (count($response[$oldIndex])) {
                    $result['data'] = $response[$oldIndex][0];
                } else {
                    $result['data'] = array();
                }

                return $result;
            }
        ], $serviceId);

        $this->serviceKernel->first($serviceId);
    }

    public function attachAudioS3URL($trackId, $trackUrl, $format, $serviceId)
    {
        $trackUrl = str_replace("%2F", "/", $trackUrl);

        $config = $this->generateConfigArray(__FUNCTION__, 'insert', $serviceId);

        $trackParts = explode("/", $trackUrl);
        for ($i = 0; $i < 5; $i++) {
            unset($trackParts[$i]);
        }
        $relativeUrl = implode("/", $trackParts);

        $config['data']['audio']['track_id']     = $trackId;
        $config['data']['audio']['url']          = $trackUrl;
     //   $config['data']['audio']['profile_id']   = $profileId;
        $config['data']['audio']['format']       = $format;
        $config['data']['audio']['relative_url'] = $relativeUrl;


        $this->serviceKernel->register([
            function() use ($config) {
                $this->send($config);
            },
            function($response, $previousIndex) {
                $result = [];
                $result['data'] = $response[$previousIndex][0];

                $cf = new CFUtils($this->agentConfig->get('secret.aws.id'), $this->agentConfig->get('secret.aws.key'));

                if (!$result) {
                    $result['data'] = array();
                } else {
                    $result['data']['url'] = $cf->makeStaticURL($result['data']['relative_url'], $this->getCFMode());
                }
                return $result;
            }
        ], $serviceId);

        $this->serviceKernel->first($serviceId);


    }

    public function setArtistGenre($profileId, $genreId, $serviceId)
    {
        $config = $this->generateConfigArray(__FUNCTION__, 'insert', $serviceId);

        $config['data']['profile_genre']['profile_id'] = $profileId;
        $config['data']['profile_genre']['genre_id'] = $genreId;

        $this->serviceKernel->register([
            function() use ($config) {
                $this->send($config);
            },
            function($response, $previousIndex) {
                $result = [];
                $result['data'] = $response[$previousIndex][0];

                return $result;
            }
        ], $serviceId);

        $this->serviceKernel->first($serviceId);
    }

    public function softDeleteTrack($trackId, $serviceId)
    {
        $config = $this->generateConfigArray(__FUNCTION__, 'update', $serviceId);

        $config['data']['track']['id'] = $trackId;
        $config['data']['track']['deleted'] = true;

        $this->serviceKernel->register([
            function() use($config, $serviceId) {
                $this->send($config);
            },
            function($response, $previousIndex) use($serviceId) {
                $result = [];
                $result['data'] = $response[$previousIndex][0];

                return $result;
            }
        ], $serviceId
        );
        $this->serviceKernel->first($serviceId);
    }

    public function updateAllGenreTracks($profileId, $genreId, $serviceId)
    {
        $config = $this->generateConfigArray(__FUNCTION__, 'update', $serviceId);
        $config['data']['track']['profile_id'] = $profileId;
        $config['data']['track_genre']['genre_id'] = $genreId;

        $this->serviceKernel->register([
            function() use($config, $serviceId) {
                $this->send($config);
            },
            function($response, $previousIndex) use($serviceId) {
                $result = [];
                if ($response[$previousIndex]) {
                    $result['data'] = $response[$previousIndex][0];
                } else {
                    $result['data'] = [];
                }

                return $result;
            }
        ], $serviceId
        );

        $this->serviceKernel->first($serviceId);
    }

    public function getArtistGenre($profileId, $serviceId)
    {
        $config = $this->generateConfigArray(__FUNCTION__, 'select', $serviceId);

        $config['data']['profile_genre']['profile_id'] = $profileId;

        $this->serviceKernel->register([
            function () use ($config) {
                $this->send($config);
            },
            function ($response, $previousIndex) {
                $result = [];
                if ($response[$previousIndex]) {
                    $result['data'] = $response[$previousIndex][0];
                } else {
                    $result['data'] = [];
                }

                return $result;
            }
        ], $serviceId);

        $this->serviceKernel->first($serviceId);
    }

    public function updateArtistGenre($profileId, $genreId, $serviceId)
    {
        $self = $this;

        $this->serviceKernel->register([
            function () use ($self, $serviceId, $profileId, $genreId) {
                $config = $this->generateConfigArray("updateArtistGenre", 'update', $serviceId);

                $config['data']['profile_genre']['profile_id'] = $profileId;
                $config['data']['profile_genre']['genre_id'] = $genreId;

                $self->send($config);
            },
            function ($response, $previousIndex) use ($self, $serviceId) {
                return ['data' => $response[0][$previousIndex]];
            }
        ], $serviceId);

        $this->serviceKernel->first($serviceId);
    }

    /**
     * returns the appropriate S3 media URL
     * @param $trackId
     * @param $mediaType
     * @return string
     */
    private function getS3url($trackId, $mediaType, $serviceId)
    {
        // get the appropriate token
        switch(strtolower($mediaType)) {
            case 'original':
                $br = 'original';
                break;
            case 'low':
                $br = '128KMP3';
                break;
            case 'high':
                $br = '320KMP3';
                break;
            case 'lossless':
                $br = 'LOSSLESS';
                break;
            default:
                $br = '128KMP3';
        }

        $config = $this->generateConfigArray(__FUNCTION__, 'select', $serviceId);

        // get the S3 location of the object from the DB
        $config['data']['audio']['track_id']    = $trackId;
        $config['data']['audio']['format']      = $br;

        $this->send($config);
    }

    public function getCFurl($trackId, $mediaType, $serviceId)
    {
        switch(strtolower($mediaType)) {
            case 'original':
                $br = 'original';
                break;
            case 'low':
                $br = '128KMP3';
                break;
            case 'high':
                $br = '320KMP3';
                break;
            case 'lossless':
                $br = 'LOSSLESS';
                break;
            default:
                $br = '128KMP3';
        }

        $config = $this->generateConfigArray("getS3url", 'select', $serviceId);

        // get the S3 location of the object from the DB
        $config['data']['audio']['track_id']    = $trackId;
        $config['data']['audio']['format']      = $br;

        $this->serviceKernel->register([
            function () use ($config, $serviceId) {
                $this->send($config);
            }
            ,
            function ($response, $previousIndex) {
                $s3url['data'] = $response[$previousIndex];

                if (!$s3url) {
                    return array();
                }

                $cf = new CFUtils($this->agentConfig->get('secret.aws.id'), $this->agentConfig->get('secret.aws.key'));

                $s3url['data'][0]['relative_url'] = $cf->makeStaticURL($s3url['data'][0]['relative_url'], $this->getCFMode());

                return $s3url;
            }
        ], $serviceId
        );

        $this->serviceKernel->first($serviceId);
    }

    /**
     * returns a time-sensitive link to a Cloudfront link for a track file
     * @param $profileId
     * @param $s3url
     * @return string
     */
    public function getExpiringTrackURL($profileId, $s3url)
    {
        $cf = new CFUtils($this->agentConfig->get('secret.aws.id'), $this->agentConfig->get('secret.aws.key'));

        // In minutes
        $expiration = 15;

        $signedURL = $cf->makeExpiringURL(
            $s3url,
            $this->getCFMode(),
            $expiration
        );

        return $signedURL;

    }

    public function addTrackGenre($trackId, $genreId, $serviceId)
    {
        $config = $this->generateConfigArray(__FUNCTION__, 'insert', $serviceId);
        $config['data']['track_genre']['track_id']    = $trackId;
        $config['data']['track_genre']['genre_id']    = $genreId;

        $this->serviceKernel->register([
            function() use ($config) {
                $this->send($config);
            },
            function($response, $previousIndex) {
                $result = [];
                $result['data'] = $response[$previousIndex][0];
                return $result;
            }
        ], $serviceId);

        $this->serviceKernel->first($serviceId);

    }

    public function listGenres($serviceId)
    {
        $config = $this->generateConfigArray(__FUNCTION__, 'select', $serviceId);

        $this->serviceKernel->register([
            function() use ($config) {
                $this->send($config);
            },
            function($response, $previousIndex) {
                $result = [];
                $result['data'] = $response[$previousIndex];

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
