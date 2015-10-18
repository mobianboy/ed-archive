<?php
namespace Eardish\DatabaseService;

use Monolog\Logger;
use Eardish\DatabaseService\DatabaseControllers\PostgresController;
use Eardish\DatabaseService\DatabaseControllers\NeoController;
use Eardish\DatabaseService\DatabaseControllers\ElasticController;

class DatabaseKernelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DatabaseKernel
     */
    protected $kernel;

    /**
     * @var DatabaseService
     */
    protected $databaseService;

    /**
     * @var PostgresController
     */
    protected $postgres;

    /**
     * @var NeoController
     */
    protected $neo;

    /**
     * @var ElasticController
     */
    protected $elastic;

    public function setUp()
    {
        //by disabling constructor we don't need to pass in a CronConnection

        $this->postgres = $this->getMockBuilder("Eardish\\DatabaseService\\DatabaseControllers\\PostgresController")
            ->disableOriginalConstructor()
            ->getMock();

        $this->neo = $this->getMockBuilder("Eardish\\DatabaseService\\DatabaseControllers\\NeoController")
            ->disableOriginalConstructor()
            ->getMock();

        $this->elastic = $this->getMockBuilder("Eardish\\DatabaseService\\DatabaseControllers\\ElasticController")
            ->disableOriginalConstructor()
            ->getMock();

        $this->databaseService = new DatabaseService($this->postgres, $this->neo, $this->elastic);
        $this->kernel = new DatabaseKernel($this->databaseService, new Logger("database"));
    }

    public function testExecute()
    {
        $this->postgres->method('select')->willReturn(
            array("data" => "i am data from the database")
        );
        // pass in client JSON
        $str = '{
                  "service": "MusicService",
                  "request": "getAlbumTracks",
                  "priority": 10,
                  "operation": "select",
                  "limit": "",
                  "orderBy": "",
                  "currentUser": "",
                  "data": {
                    "album_track": {
                      "track_id": "",
                      "album_id": "98",
                      "album_track_id": "",
                      "track_num": ""
                    }
                  }
                }';

        $this->assertEquals(
            array("data" => "i am data from the database"),
            $this->kernel->execute($str)
        );
    }

    public function testInsertQuery()
    {
        $this->postgres->method('insert')->willReturn(
            array("0" => array("data" => "i am data from the database"))
        );

        $clientData = array(
            "service" => "RecommendationService",
            "request" => "createProfileGenreBlend",
            "operation" => "insert",
            "priority" => 10,
            "multi" => true,
            "data" => array(
                "profile_genre_blend" => array(
                    array(
                        'profile_id' => 3,
                        'genre_id' => 1,
                        'weight' =>  2
                    ),
                    array(
                        'profile_id' => 3,
                        'genre_id' => 5,
                        'weight' =>  2
                    ),
                    array(
                        'profile_id' => 3,
                        'genre_id' => 3,
                        'weight' =>  2
                    ),
                    array(
                        'profile_id' => 3,
                        'genre_id' => 6,
                        'weight' =>  0
                    )
                )
            )
        );
        $this->assertEquals(
            array("0" => array("data" => "i am data from the database")),
            $this->databaseService->buildQuery($clientData)
        );
    }

    public function testAnalyticsSubmit()
    {
        $this->postgres->method('insert')->willReturn(
            array("0" => array(
                'id' => '8',
                'device_type' => 'tablet',
                'device_make' => 'iPad',
                'device_model' => 'iPad Air 2014',
                'device_carrier' => null,
                'device_os' => 'iOS 8.1',
                'client_version' => ".8",
                'latitude' => '37.9',
                'longitude' => '115.2',
                'time' => '1993-01-16 08:30:37',
                'user_id' => '3',
                'view_route' => "users/getProfile",
                'track_id' => '40',
                'track_timecode' => "03:20",
                'session_duration' => '340',
                'event_type' => "pause"
            ))
        );

        $data = array(
            "multi" => false,
            "data" =>
                array('analytic' => array(
                'device_type' => 'tablet',
                'device_make' => 'iPad',
                'device_model' => 'iPad Air 2014',
                'device_carrier' => null,
                'device_os' => 'iOS 8.1',
                'client_version' => ".8",
                'latitude' => 37.9,
                'longitude' => 115.2,
                'time' => '1993-01-16 08:30:37',
                'user_id' => 3,
                'view_route' => "users/getProfile",
                'track_id' => 40,
                'track_timecode' => "03:20",
                'session_duration' => 340,
                'event_type' => "pause")
            )
        );
        $columns = array('device_type', 'device_make', 'device_model', 'device_carrier', 'device_os', 'client_version', 'latitude', 'longitude', 'time', 'user_id', 'view_route', 'track_id', 'track_timecode', 'session_duration', 'event_type');

        $this->assertEquals(
            array(
                0 => array(
                    'id' => '8',
                    'device_type' => 'tablet',
                    'device_make' => 'iPad',
                    'device_model' => 'iPad Air 2014',
                    'device_carrier' => null,
                    'device_os' => 'iOS 8.1',
                    'client_version' => '.8',
                    'latitude' => '37.9',
                    'longitude' => '115.2',
                    'time' => '1993-01-16 08:30:37',
                    'user_id' => '3',
                    'view_route' => 'users/getProfile',
                    'track_id' => '40',
                    'track_timecode' => '03:20',
                    'session_duration' => '340',
                    'event_type' => 'pause'
                )
            ),
            $this->databaseService->insertEntry($data, 'analytic', $columns)
        );
    }
}
