<?php
namespace Eardish\MusicService;

use Eardish\AppConfig;

class MusicServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MusicService;
     */
    protected $service;
    protected $connection;
    protected $config;

    public function setUp()
    {
        $this->connection = $this->getMockBuilder("Eardish\\MusicService\\Core\\Connection")->getMock();
        $config = new AppConfig('/eda/secret/app.json', 'local');
        $this->service = new MusicService($this->connection, $config);
    }

    public function testGetArtistTracks()
    {
        $this->connection->method('sendToDB')->willReturn(
            [
                'success' => true,
                'data' => [
                    0 => [
                        'id' => 53,
                        'profile_id' => 95,
                        'created_by_id' => 22,
                        'name' => 'Architecto totam corrupti',
                        'length' => 3,
                        'play_count' => 10494,
                        'waveform_image_loc' => 'http://lorempixel.com/640/480/?93519',
                        'deleted' => true,
                    ],
                ]
            ]
        );

        $result = $this->service->getArtistTracks(95);

        $this->assertInternalType('array', $result);

        $this->assertTrue($result['success']);

        $this->assertArrayHasKey('name', $result['data']['0']);


    }

    public function testAddTrack()
    {
        $this->connection->method('sendToDB')->willReturn(
            [
                'success' => true,
                'data' => [
                    0 => [
                        'id' => 48,
                        'profile_id' => 12,
                        'art_id' => 24,
                        'name' => 'Superman',
                        'length' => 320,
                        'deleted' => false,
                        'date_created' => '2015-12-03 22:04:34',
                        'date_modified' => '2015-12-23 12:04:54'
                    ]
                ]
            ]
        );

        $result = $this->service->addTrack(12, 'Superman', 24);

        $this->assertEquals(
            $result['data']['name'], 'Superman'
        );
    }

    public function testDeleteTrack()
    {
        $this->connection->method('sendToDB')->willReturn(
            [
                'success' => true
            ]
        );

        $result = $this->service->deleteTrack(12, 7);
        $this->assertEquals(
            true, $result['success']
        );
    }

    public function testRateTrack()
    {
        $this->connection->method('sendToDB')->willReturn(
            [
                'success' => true
            ]
        );

        $result = $this->service->rateTrack(12, 7, 4);
        $this->assertEquals(
            true, $result['success']
        );

    }

    public function testAddTrack()
    {
        $artistProfileId = 200;
        $trackName = 'Californication';
        $trackArtId = 14;

        $this->connection->method('sendToDB')->willReturn(
            [
                'success' => true,
                'data' => [
                    0 => [
                        'id' => 100,
                        'profile_id' => $artistProfileId,
                        'art_id' => $trackArtId,
                        'name' => $trackName
                    ]
                ]]
        );

        $result = $this->service->updateTrackRating(12, 7, 5);
        $this->assertEquals(
            true, $result
        );
    }

//    public function testDeleteTrack()
//    {
//        $trackId = 10;
//        $profileId = 50;
//
//        $this->assertEquals(
//            ['success' => true], $this->service->deleteTrack($trackId, $profileId)
//        );
//    }
//
    public function testUpdateTrackRating()
    {
        $profileId = 15;
        $trackId = 67;
        $rating = 3;

        $this->connection->method('sendToDB')->willReturn(
            [
                'success' => true
            ]
        );

        $this->assertEquals(
            true, $this->service->updateTrackRating($profileId, $trackId, $rating)
        );
    }
//
//    public function testGetTrackDetail()
//    {
//        $trackId = 4;
//        $profileId = 20;
//
//        $this->assertEquals(
//            ['success' => true], $this->service->getTrackDetail($trackId, $profileId)
//        );
//    }
//
    public function testGetProfileTrackRating()
    {
        $trackId = 24;
        $profileId = 30;

        $this->connection->method('sendToDB')->willReturn(
            [
                'success' => true,
                'data' => [
                    0 => [
                        'profile_id' => $profileId,
                        'track_id' => $trackId
                    ]
                ]
            ]
        );

        $this->assertEquals(
            ['data' => [
                'profile_id' => $profileId,
                'track_id' => $trackId
                ]],
            $this->service->getProfileTrackRating($trackId, $profileId)
        );
    }
//
//    public function testGetAlbumTracks()
//    {
//        $albumId = 7;
//
//        $this->assertEquals(
//            ['success' => true], $this->service->getAlbumTracks($albumId)
//        );
//    }
//
//
//    public function testGetArtistAlbums()
//    {
//        $profileId = 30;
//
//        $this->assertEquals(
//            ['success' => true], $this->service->getArtistAlbums($profileId)
//        );
//    }
//
//
//    public function testCreateAlbum()
//    {
//        $profileId = 15;
//        $albumName = 'Stadium Arcadium';
//
//        $this->assertEquals(
//            ['success' => true], $this->service->createAlbum($profileId, $albumName)
//        );
//    }

/**
    public function testMediaURLSigning()
    {
        $url = "media/dg1.mp3";
        $pId = 10;
        $exUrl = $this->service->getExpiringTrackURL($pId,$url);
        $this->assertNotNull($exUrl);

    }
*/
}
