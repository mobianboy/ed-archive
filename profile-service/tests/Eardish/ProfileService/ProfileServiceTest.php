<?php
namespace Eardish\ProfileService;


use Eardish\AppConfig;


class ProfileServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ProfileService
     */
    protected $service;
    protected $connection;
    protected $config;

    public function setUp()
    {
        $this->connection = $this->getMockBuilder("Eardish\\ProfileService\\Core\\Connection")->getMock();
        $config = new AppConfig('/eda/secret/app.json', 'local');
        $this->service = new ProfileService($this->connection, $config);
    }

    public function testGetFullNameByEmail()
    {
        $name = "Matt Murdock";
        $this->connection->method("sendToDB")->willReturn($name);
        $result = $this->service->getFullNameByEmail("imnotdaredevil@marvel.com");
        $this->assertEquals($name, $result);
    }

    public function testCreateProfile()
    {
        $profileData = [
            'id' => 203,
            'user_id' => 102,
            'art_id' => 1,
            'contact_id' => 102,
            'onboarded' => true,
            'type' => "fan",
            'first_name' => "Person",
            'last_name' => "Name",
            'artist_name' => 'PN',
            'year_of_birth' => "1988",
            'bio' => "A bio goes here"
        ];

        $this->connection->method('sendToDB')
            ->willReturn(
                [
                    'success' => true,
                    'count' => 1,
                    'data' => [
                        0 => [
                            'id' => 203
                        ]
                ]]
            );

        $this->assertEquals(
            ['profileID' => 203,
                'data' => [
                    0 => ['id' => 203]
                ]],
            $this->service->createProfile($profileData)
        );
    }

    public function testCreateContactInfo()
    {
        $contactInfo = [
            'id' => 203,
            'contact_id' => 102,
        ];

        $this->connection->method('sendToDB')->will($this->onConsecutiveCalls(
            [
                "success" => true,
                "data" => [
                    0 => [
                        'id' => 102]]
            ]
        ));

        $this->assertEquals(
            ['contactId' => 102],
            $this->service->createContactInfo($contactInfo)
        );
    }

    public function testEditContactInfo()
    {
        $contactInfo = [
            'id' => 203,
            'contact_id' => 106,
        ];

        $this->connection->method('sendToDB')->will($this->onConsecutiveCalls(
            [
                "success" => true,
                "data" => [
                    0 => [
                        'id' => 106]]
            ]
        ));

        $this->assertEquals(
            ['id' => 106],
            $this->service->editContactInfo($contactInfo)
        );
    }

    public function testGetArtistContent()
    {
        $profileId = 206;

        $this->connection->method('sendToDB')->will($this->onConsecutiveCalls(
            [
                "success" => true,
                "data" => [
                    0 => [
                        'id' => 206]]
            ]
        ));

        $this->assertEquals(
            ['data' => [
                0 => ['id' => 206]
            ]],
            $this->service->getArtistContent($profileId)
        );
    }

    public function testGetSenderByInviteCode()
    {
        $inviteCode = '12345abcde';

        $this->connection->method('sendToDB')->will($this->onConsecutiveCalls(
            [
                "success" => true,
                "data" => [
                    0 => [
                        'id' => 206]]
            ]
        ));

        $this->assertEquals(
            ["success" => true, "data" => [0 => ['id' => 206]]],
            $this->service->getSenderByInviteCode($inviteCode)
        );
    }

/*
    public function testCreateArtistProfile()
    {
        $params = array(EDConnectionException
            'type' => 'artist-group',
            'genre' => 'Alternative',
            'artistName' => 'Meet Me In Montauk',
            'hometown' => 'Los Angeles',
            'firstName' => 'Tony',
            'lastName' => 'Stark',
            'arRepName' => 'Nick Fury',
            'email' => 'mmim@gmail.com',
            'phone' => '8184953208'
        );

        $this->connection->method("sendToDB")->willReturn(5);

        $this->assertInternalType(
            'array',
            $this->service->createArtistProfile($params), 5
        );
    }
*/
    public function testSelectProfile()
    {
        $this->connection->method("sendToDB")->willReturn(array(
            'success' => true,
            'data' =>
            array(
                'type' => 'artist-grEDConnectionExceptionoup',
                'genre' => 'Alternative',
                'artistName' => 'Meet Me In Montauk',
                'hometown' => 'Los Angeles',
                'firstName' => 'Tony',
                'lastName' => 'Stark',
                'arRepName' => 'Nick Fury',
                'email' => 'mmim@gmail.com',
                'phone' => '8184953208'
            )
        ));

        $result = $this->service->selectProfile(5);

        $this->assertInternalType(
            'array', $result
        );

        $this->assertArrayHasKey(
            'type', $result['data']
            );
        $this->assertArrayHasKey(
            'firstName', $result['data']
        );
        $this->assertArrayHasKey(
            'email', $result['data']
        );
    }

    public function testUpdateProfileIsOnboarded()
    {
        $profileId = 203;

        $this->connection->method('sendToDB')->will($this->onConsecutiveCalls(
            ['success' => true,
                'data' => [
                    0 => ['id' => 203,
                        'onboarded' => true]
                ]]
        ));

        $this->assertEquals(
            true,
            $this->service->updateProfileIsOnboarded($profileId)
        );
    }

    public function testEditArtistProfile()
    {
        $profileData = [
            'first_name' => "Dude",
            'last_name' => "Person",
            'artist_name' => 'DP',
        ];

        $this->connection->method('sendToDB')
            ->willReturn(
                [
                    'success' => true,
                    'count' => 1,
                    'data' => [
                        0 => [
                            'id' => 203
                        ]
                    ]]
            );

        $this->assertEquals(
            ['data' => ['id' => 203]],
            $this->service->editArtistProfile($profileData)
        );
    }

    public function testDeleteArtistProfile()
    {
        $profileId = 203;

        $this->connection->method('sendToDB')->will($this->onConsecutiveCalls(
            ['success' => true,
                'data' => [
                    0 => ['id' => 203]
                ]]
        ));

        $this->assertEquals(
            ["profile-deleted" => ['success' => true,
                'data' => [
                    0 => ['id' => 203]
                ]]],
            $this->service->deleteArtistProfile($profileId)
        );
    }

    public function testAddArt()
    {
        $profileId = 203;
        $artLoc = "https://s3-us-west-2.amazonaws.com/eardish.dev.images/public/profile";

        $this->connection->method('sendToDB')->will($this->onConsecutiveCalls(
            ['success' => true,
                'data' => [
                    0 => ['id' => 203]
                ]]
        ));

        $this->assertEquals(
            ["art-added" => ['success' => true,
                'data' => [
                    0 => ['id' => 203]
                ]]],
            $this->service->addArt($profileId, $artLoc)
        );
    }

    public function deleteArt()
    {
        $profileId = 203;
        $artLoc = "https://s3-us-west-2.amazonaws.com/eardish.dev.images/public/profile";

        $this->connection->method('sendToDB')->will($this->onConsecutiveCalls(
            ['success' => true,
                'data' => [
                    0 => ['id' => 203]
                ]]
        ));

        $this->assertEquals(
            ["art-deleted" => ['success' => true,
                'data' => [
                    0 => ['id' => 203]
                ]]],
            $this->service->deleteArt($profileId, $artLoc)
        );
    }

    public function testListArtistProfiles()
    {
        $this->connection->method('sendToDB')->will($this->onConsecutiveCalls(
            ['success' => true,
                'data' => [
                    0 => ['id' => 51],
                    1 => ['id' => 52],
                    2 => ['id' => 53],
                    3 => ['id' => 54],
                    4 => ['id' => 55]
            ]]
        ));

        $this->assertEquals(
            ['data' => [
                0 => ['id' => 51],
                1 => ['id' => 52],
                2 => ['id' => 53],
                3 => ['id' => 54],
                4 => ['id' => 55]
            ]],
            $this->service->listArtistProfiles()
        );
    }

//    public function getArtUrls($artId) {
//
//    }

/*
    public function testSendToDbFirstException()
    {
        $conn = new Core\Connection();
        $conn->start("localhost",11220);
        $conn->sendToDB("blowUp");

    }
*/



}
