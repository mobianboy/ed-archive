<?php
//namespace Eardish\DatabaseService;
//
//use Eardish\DatabaseService\DatabaseControllers\ElasticController;
//use Eardish\DatabaseService\DatabaseControllers\NeoController;
//use Eardish\DatabaseService\DatabaseControllers\PostgresController;
//
//class DatabaseServiceTest extends \PHPUnit_Framework_TestCase
//{
//
//    /**
//     * @var DatabaseService
//     */
//    protected $dbService;
//
//    public function setUp()
//    {
//        $this->dbService = new DatabaseService(new PostgresController(new CronConnection()), new NeoController(), new ElasticController());
//    }
//
//    public function testBuildQueryPostgres()
//    {
//        $clientData = array(
//            "service" => "ProfileService",
//            "request" => "createArtistProfile",
//            "operation" => "insert",
//            "limit" => "10",
//            "multi" => false,
//            "priority" => '10',
//            "data" => array(
//                "profile" => array(
//                    "user_id" => 12,
//                    "type" => "artist",
//                    "profile_image" => "www.heresmypic.com",
//                    "genre_id" => 3,
//                    "artist_name" => "MMIM",
//                    "influenced_by" => null,
//                    "year_founded" => null,
//                    "hometown" => "LA",
//                    "first_name" => "Peter",
//                    "last_name" => "Parker",
//                    "website" => null,
//                    "ar_rep_name" => "Nick Fury",
//                    "email" => "imnotspiderman@marvel.com",
//                    "phone" => "3232854978",
//                    "zipcode" => "90032",
//                    "artist_bio" => "Heres some stuff",
//                    "address" => "5041 Awesome Ave",
//                    "address_line_2" => null,
//                    "city" => "LA",
//                    "state" => "CA",
//                    "facebook_page" => null,
//                    "twitter_page" => null
//                ),
//
//            )
//        );
//
////        $this->assertEquals(
////            array(),
//           $this->dbService->buildQuery($clientData);
////        );
//    }
//}
////
////    public function testBuildQueryPostgres()
////    {
////        $clientData = array(
////            "service" => "UserService",
////            "request" => "getUserAlbums", //deleteUserTrack  addTrackToUse
////            "action" => "select",
////            "limit" => "10",
////            "order" => "created_at",
////            "direction" => "asc",
////            "priority" => '10',
////            "data" => array(
////                "user_album" => array(
////                    "user_id" => 20
////                )
////            )
////        );
////
////        $this->assertEquals(
////            array(),
////            $this->dbService->buildQuery($clientData)
////        );
////    }
////
////    public function testBuildQueryElastic()
////    {
////
////        $clientData = array(
////            "service" => "music",
////            "request" => "newReleases",
////            "action" => "select",
////            "limit" => "10",
////            "order" => "posted_at",
////            "direction" => "asc",
////            "data" => array(
////                "track_plays" => array(
////                    "user_id" => "12345"
////                )
////            )
////        );
////
////        $this->assertEquals(
////           array("range" => array("release_date" => array("gte" => '$time', "lte" => "now", "time_zone" => "-10:00"))),
////           $this->dbService->buildQuery($clientData)
////        );
////    }
////
////    public function testBuildQueryNeo()
////    {
////        $clientData = array(
////            "service" => "music",
////            "request" => "getUserTracks",
////            "action" => "select",
////            "limit" => "20",
////            "data" => array(
////                "user" => array(
////                    "id" => 2
////                ),
////                "user_track" => array(
////                    "user_id" => 5
////                )
////            )
////        );
////
////        $this->assertEquals(
////            array(),
////            $this->dbService->buildQuery($clientData)
////        );
////    }
////
////    public function testReturnPassword()
////    {
////        $result = $this->dbService->returnPassword(array("username" => 'Nolan.Jaydon'));
////        $this->assertInternalType('string', $result);
////    }
////
////    public function testUpdatePassword()
////    {
////        $result = $this->dbService->updatePassword(array("username" => 'Nolan.Jaydon', "password" => "password"));
////        $this->assertEquals($result, "password");
////    }
////
////    public function testReturnID()
////    {
////        $result = $this->dbService->returnID(array("username" => 'Nolan.Jaydon'));
////        $this->assertEquals($result, "1");
////    }
////
////}