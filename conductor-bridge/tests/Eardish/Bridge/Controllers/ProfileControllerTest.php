<?php
namespace Eardish\Bridge\Controllers;

use Eardish\DataObjects\Blocks\MetaBlock;

class ProfileControllerTest extends ControllerTestCase
{
    /**
     * @var $controller ProfileController
     */
    protected $controller;
    protected $connection2;

    public function setUp()
    {
        parent::setUp();
//        $this->controller = new ProfileController(
//            $this->profileAgent,
//            $this->musicAgent,
//            $this->imageProcessingAgent,
//            $this->userAgent,
//            $this->recommendationAgent
//        );

        $this->dto->getDataBlock()->setDataArray(array(
            'password' => 'eardishrocks',
            'password-confirmation' => 'eardishrocks',
            'email' => 'test@eardish.com',
            'first-name' => 'senor',
            'last-name' => 'dish',
            'year-of-birth' => 1965,
            'zipcode' => '95607',
            'invite-code' => 'k6g77d3'
        ));
    }

    public function testSelectProfile()
    {
        $this->connection->expects($this->any())->method('send')
            ->will($this->onConsecutiveCalls(
                array('data' =>
                    array(0 => array(
                        "type" =>'artist',
                        "art_id" => 1
                    ))
                ),
                array('data' =>
                    array(0 => array("original_url" => "aws.picture.com")
                    )
                )
            ));

        $this->dto->getDataBlock()->setDataArray(array(
            'id' => 5,
        ));
        $this->dto->getRouteBlock()->setControllerMethod("selectProfile");
        $this->dto->getRouteBlock()->setControllerName("Profile");

        $bridgeKernel = $this->newBridgeKernel();

        $this->assertEquals(
            array(
                'data' => array(
                    'type' => 'artist',
                    'art_id' => 1,
                    'art_url' => 'aws.picture.com'
                ),
                'modelType' => 'profile-artist'
            )
            ,
           $bridgeKernel->inbound($this->dto)
        );
    }

    public function testCreateUserGenreBlend()
    {
        $this->dto->getDataBlock()->setDataArray(array(
            'genres-liked' => ['Alternative', 'Pop', 'Rock'],
            'genres-disliked' => ['Country'],
        ));

        $this->dto->getRouteBlock()->setControllerMethod("createProfileGenreBlend");
        $this->dto->getRouteBlock()->setControllerName("Profile");
        $metaBlock = new MetaBlock();
        $metaBlock->setCurrentProfile(3);
        $metaBlock->setCurrentUser(3);
        $this->dto->injectBlock($metaBlock);

        $this->connection->method('send')->willReturn(true);

        $bridgeKernel = $this->newBridgeKernel();

        $this->assertEquals(
            array(
                'data'=> true,
                'modelType' => 'genre-blend'
                ),
            $bridgeKernel->inbound($this->dto)
        );
    }


    public function testModifyUserBlend()
    {
        $this->dto->getDataBlock()->setDataArray(array(
            'genres-liked' => ['Country', 'Alternative', 'Rock'],
            'genres-disliked' => ['Pop'],
        ));

        $this->connection->method('send')->willReturn(true);

        $this->dto->getRouteBlock()->setControllerMethod("modifyProfileGenreBlend");
        $this->dto->getRouteBlock()->setControllerName("Profile");
        $metaBlock = new MetaBlock();
        $metaBlock->setCurrentProfile(3);
        $metaBlock->setCurrentUser(3);
        $this->dto->injectBlock($metaBlock);

        $bridgeKernel = $this->newBridgeKernel();

        $this->assertEquals(
            array(
                'data'=> true,
                'modelType' => 'genre-blend'
            ),
            $bridgeKernel->inbound($this->dto)
        );
    }

    public function testCreateArtistProfile()
    {
        $this->dto->getRouteBlock()->setControllerMethod("createArtistProfile");
        $this->dto->getRouteBlock()->setControllerName("Profile");
        $this->dto->getMetaBlock()->setCurrentUser(5);
        $this->dto->getDataBlock()->setDataArray(array(
            'type' => 'artist-group',
            'genre' => 'Alternative',
            'artistName' => 'Meet Me In Montauk',
            'hometown' => 'Los Angeles',
            'name' => array(
                'first' => 'Tony',
                'last' => 'Stark'
            ),
            'arRep' => 'Nick Fury',
            'email' => 'mmim@gmail.com',
            'phone' => '8184953208'
        ));

        $this->connection->expects($this->any())->method('send')
            ->will($this->onConsecutiveCalls(
                array("userID" => 3),
                array("contactId" => 7),
                array("data" =>
                    array(
                        "id" => 27,
                        "type" => 'artist-solo',
                        "artistName" => 'beatles'
                    ),
                    "profileID" => 27,

                ),
                true
            ));

        $bridgeKernel = $this->newBridgeKernel();

        $this->assertEquals(
            array(
                'data'=> array(
                    "id" => 27,
                    'type' => "artist-solo",
                    'artistName' => 'beatles'
                ),
                'modelType' => 'artist-solo'
            ),
            $bridgeKernel->inbound($this->dto)
        );
    }

    public function testEditProfile()
    {
        $this->dto->getRouteBlock()->setControllerMethod("editArtistProfile");
        $this->dto->getRouteBlock()->setControllerName("Profile");
        $this->dto->getDataBlock()->setDataArray(array(
            'id' => 17,
            'website' => "facebook.com",
            'type' => 'fan',
            'genre' => 5,
            'artistName' => 'Wilco',
            'hometown' => "Chicago, Il",
            'name' => array(
                'first' => 'Jeff',
                'last' => 'Tweedy'
            ),
            'arRep' => 'Nels Cline',
            'email' => 'wilco@wilcoworld.net',
            'contactId' => 3,
            'phone' => '831221313'
        ));

        $this->connection->expects($this->any())->method('send')
            ->will($this->onConsecutiveCalls(
                array('data' => array(
                    'id' => 17,
                    'type' => 'fan',
                    'genre' => 5,
                    'website' => 'facebook.com',
                    'artist_name' => 'Wilco',
                    'hometown' => "Chicago, Il",
                    'first_name' => 'Jeff',
                    'last_name' => 'Tweedy',
                    'ar_rep' => 'Nels Cline',
                    'email' => 'wilco@wilcoworld.net'
                )),
                array("contactId" => 3)
            )
        );

        $bridgeKernel = $this->newBridgeKernel();

        $this->assertEquals(
            array('data'=>
                array(
                    'id' => 17,
                    'type' => 'fan',
                    'genre' => 5,
                    'website' => 'facebook.com',
                    'artist_name' => 'Wilco',
                    'hometown' => "Chicago, Il",
                    'first_name' => 'Jeff',
                    'last_name' => 'Tweedy',
                    'ar_rep' => 'Nels Cline',
                    'email' => 'wilco@wilcoworld.net'
                ),
                "modelType" => 'fan'
            ),
            $bridgeKernel->inbound($this->dto)
        );
    }

    public function testAddArt()
    {
        $this->dto->getRouteBlock()->setControllerMethod("addArt");
        $this->dto->getRouteBlock()->setControllerName("Profile");
        $this->dto->getMetaBlock()->setCurrentProfile(3);
        $this->dto->getDataBlock()->setDataArray(array(
            'track-art-location' => 'https://images.s3.amazonaws.com/ProfileArt'
        ));

        $this->connection->method('send')->willReturn(['success' => true]);
        $bridgeKernel = $this->newBridgeKernel();
        $this->assertEquals(
            ['data' => array()],
            $bridgeKernel->inbound($this->dto)
        );
    }
}
