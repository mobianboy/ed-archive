<?php
namespace Eardish\AnalyticsService;

use Eardish\AppConfig;
use Eardish\AnalyticsService\Core\Connection;

class AnalyticsServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AnalyticsService
     */
    protected $service;
    protected $config;

    /**
     * @var Connection
     */
    protected $connection;

    public function setUp()
    {
        $this->connection = $this->getMockBuilder("Eardish\\AnalyticsService\\Core\\Connection")->getMock();
        $config = new AppConfig('/eda/secret/app.json', 'local');
        $this->service = new AnalyticsService($this->connection, $config);
    }

    public function testSubmitEntry()
    {
        $data = array(
            "common" => array(
                "device" => array(
                    "type" => "tablet",
                    "make" => "iPad",
                    "model" => "iPad Air 2014",
                    "OS" => "iOS 8.1"),
                "client-version" => ".8",
                "location" => array(
                    "latitude" => "35.7",
                    "longitude" => "115.8",
                ),
                "time" => '1993-01-16 08:30:37',
                "user" => 9,
                "view-route" => "user/profile/6",
                "viewState" => array(
                    "player-state" => array(
                        "song-id" => 30,
                        "playing" => true,
                        "timecode" => "02:49"
                    )
                ),
                "session" => array(
                    "duration" => 1003
                )
            ),
            "event" => array(
                "type" => "rate",
                "values" => array(
                    "key" => "value"
                )
            )
        );

        $this->assertEquals(
            array(
                'service' => 'AnalyticsService',
                'request' => 'submitEntry',
                'operation' => 'insert',
                'priority' => null,
                'data' => [
                'analytic' => array(
                    'device_type' => 'tablet',
                    'device_make' => 'iPad',
                    'device_model' => 'iPad Air 2014',
                    'device_carrier' => null,
                    'device_os' => 'iOS 8.1',
                    'device_uuid' => null,
                    'latitude' => null,
                    'longitude' => null,
                    'time' => '1993-01-16 08:30:37',
                    'user_id' => 9,
                    'view_route' => null,
                    'profile_id' => null,
                    "player-state" => '{"song-id":30,"playing":true,"timecode":"02:49"}',
                    'session_duration' => 1003,
                    'event_type' => 'rate',
                    'values' => '{"key":"value"}',
                    'client_version' => null
                )]
            ),
            $this->service->submitEntry($data)
        );
    }

    public function testGetUserStats()
    {
        $userId = 203;
        $event = 'completedListen';

        $this->connection->method('sendToDB')->will($this->onConsecutiveCalls(
            [
                "data" => [
                    0 => [
                        'id' => 203]]
            ]
        ));

        $this->assertEquals(
            [0 => ['id' => 203]],
            $this->service->getUserStats($userId, $event)
        );
    }

    public function testGetCompletedListensChart()
    {
        $start = "2015-05-31T00:00:00-07:00";
        $stop = "2015-06-06T23:59:59-07:00";
        $groupBy = "track_id";

        $this->connection->method('sendToDB')->will($this->onConsecutiveCalls(
            [
                'success' => true,
                'count' => 3,
                'data' => [
                    0 => [
                        'id' => "19",
                        'value' => "2"],
                    1 => [
                        'id' => "29",
                        'value' => "1"],
                    2 => [
                        'id' => "39",
                        'value' => "1"],
                    3 => [
                        'id' => "49",
                        'value' => "2"],
            ]]
        ));

        $this->assertEquals(
            [0 => [
                'id' => "19",
                'value' => "2"],
                1 => [
                    'id' => "29",
                    'value' => "1"],
                2 => [
                    'id' => "39",
                    'value' => "1"],
                3 => [
                    'id' => "49",
                    'value' => "2"]],
            $this->service->getCompletedListensChart($start, $stop, $groupBy)
        );
    }

    public function testGetHighestRatedChart()
    {
        $start = "2015-06-31T00:00:00-07:00";
        $stop = "2015-05-06T23:59:59-07:00";
        $groupBy = "track_id";

        $this->connection->method('sendToDB')->will($this->onConsecutiveCalls(
            [
                'success' => true,
                'count' => 3,
                'data' => [
                    0 => [
                        'id' => "12",
                        'value' => "2"],
                    1 => [
                        'id' => "22",
                        'value' => "1"],
                    2 => [
                        'id' => "32",
                        'value' => "1"],
                    3 => [
                        'id' => "42",
                        'value' => "2"],
                ]]
        ));

        $this->assertEquals(
            [0 => [
                'id' => "12",
                'value' => "2"],
                1 => [
                    'id' => "22",
                    'value' => "1"],
                2 => [
                    'id' => "32",
                    'value' => "1"],
                3 => [
                    'id' => "42",
                    'value' => "2"]],
            $this->service->getHighestRatedChart($start, $stop, $groupBy)
        );
    }

    public function testGetCompletedListensFans()
    {
        $start = "2015-06-31T00:00:00-07:00";
        $stop = "2015-05-06T23:59:59-07:00";

        $this->connection->method('sendToDB')->will($this->onConsecutiveCalls(
            [
                'success' => true,
                'count' => 3,
                'data' => [
                    0 => [
                        'id' => "10",
                        'value' => "2"],
                    1 => [
                        'id' => "20",
                        'value' => "1"],
                    2 => [
                        'id' => "30",
                        'value' => "1"],
                    3 => [
                        'id' => "40",
                        'value' => "2"],
                ]]
        ));

        $this->assertEquals(
            [0 => [
                'id' => "10",
                'value' => "2"],
            1 => [
                'id' => "20",
                'value' => "1"],
            2 => [
                'id' => "30",
                'value' => "1"],
            3 => [
                'id' => "40",
                'value' => "2"]],
            $this->service->getCompletedListensFans($start, $stop)
        );
    }

    public function testGetMostTracksRatedFans()
    {
        $start = "2015-06-31T00:00:00-07:00";
        $stop = "2015-05-06T23:59:59-07:00";

        $this->connection->method('sendToDB')->will($this->onConsecutiveCalls(
            [
                'success' => true,
                'count' => 3,
                'data' => [
                    0 => [
                        'id' => "50",
                        'value' => "2"],
                    1 => [
                        'id' => "60",
                        'value' => "1"],
                    2 => [
                        'id' => "70",
                        'value' => "1"],
                    3 => [
                        'id' => "80",
                        'value' => "2"],
                ]]
        ));

        $this->assertEquals(
            [0 => [
                'id' => "50",
                'value' => "2"],
            1 => [
                'id' => "60",
                'value' => "1"],
            2 => [
                'id' => "70",
                'value' => "1"],
            3 => [
                'id' => "80",
                'value' => "2"]],
            $this->service->getMostTracksRatedFans($start, $stop)
        );
    }

    public function testDistributeBadges()
    {
        $badgeWinners = [0 => [
                            'id' => 50],
                        1 => [
                            'id' => 60],
                        2 => [
                            'id' => 70],
                        3 => [
                            'id' => 80]];

        $this->connection->method('sendToDB')->will($this->onConsecutiveCalls(
            [
                "data" => [
                    0 => [
                        'id' => 50],
                    1 => [
                        'id' => 60],
                    2 => [
                        'id' => 70],
                    3 => [
                        'id' => 80]]
            ]
        ));

        $this->assertEquals(
            [0 => [
                'id' => 50],
            1 => [
                'id' => 60],
            2 => [
                'id' => 70],
            3 => [
                'id' => 80]],
            $this->service->distributeBadges($badgeWinners)
        );
    }

    public function testGetBadges()
    {
        $this->connection->method('sendToDB')->will($this->onConsecutiveCalls(
            [
                "data" => [
                    0 => [
                        'completed-listens-track'],
                    1 => [
                        'most-tracks-rated-fan'],
                    2 => [
                        'highest-rated-track'],
                    3 => [
                        'highest-rated-artist'],
                    4 => [
                        'completed-listens-artist'],
                    5 => [
                        'completed-listens-fan']
                ]
            ]
        ));

        $this->assertEquals(
            ["data" => [
                0 => [
                    'completed-listens-track'],
                1 => [
                    'most-tracks-rated-fan'],
                2 => [
                    'highest-rated-track'],
                3 => [
                    'highest-rated-artist'],
                4 => [
                    'completed-listens-artist'],
                5 => [
                    'completed-listens-fan']
            ]],
            $this->service->getBadges()
        );
    }

    public function testGetProfileBadges()
    {
        $profileId = 100;

        $this->connection->method('sendToDB')->will($this->onConsecutiveCalls(
            [
                "data" => [
                    0 => [
                        'completed-listens-track'],
                    1 => [
                        'most-tracks-rated-fan'],
                    2 => [
                        'highest-rated-track'],
                    3 => [
                        'completed-listens-fan']
                ]
            ]
        ));

        $this->assertEquals(
            ["data" => [
                0 => [
                    'completed-listens-track'],
                1 => [
                    'most-tracks-rated-fan'],
                2 => [
                    'highest-rated-track'],
                3 => [
                    'completed-listens-fan']
            ]],
            $this->service->getProfileBadges($profileId)
        );
    }
}
