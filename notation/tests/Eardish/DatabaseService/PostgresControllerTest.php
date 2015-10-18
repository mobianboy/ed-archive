<?php
//namespace Eardish\DatabaseService\DatabaseControllers;
//
//use Eardish\DatabaseService\CronConnection;
//use Eardish\DatabaseService\DatabaseControllers\Models;
//use Eardish\DatabaseService\DatabaseService;
//use Faker\Factory;
//use Monolog\Logger;
//
//class PostgresControllerTest extends \PHPUnit_Framework_TestCase
//{
//    /**
//     * @var PostgresController
//     */
//    protected $postgres;
//    protected $entityManager;
//    protected $query;
//    protected $conn;
//    protected $queryBuilder;
//
//    /**
//     * @var DatabaseService
//     */
//    protected $databaseService;
//
//    protected $dto;
//
//    public function setUp()
//    {
//        $this->postgres = new PostgresController(new CronConnection());
//        $this->databaseService = new DatabaseService($this->postgres, new NeoController(), new ElasticController());
//
//    }
//    public function testRedeemInviteCodeQuery()
//    {
//        $clientData = array(
//            "service" => "UserService",
//            "request" => "redeemInviteCode",
//            "operation" => "update",
//            "limit" => "20",
//            "priority" => 10,
//            "data" => array(
//                "invite_code" => array(
//                    "invitee_id" => "50",
//                    "invite_code" => "156ac443"
//                )
//            )
//        );
//
//        $this->assertEquals(
//            array("0" => array("data" => "i am data from the database")),
//            $this->databaseService->buildQuery($clientData)
//        );
//    }
//
//
//
//
//
//
////    Insert some data in to user table
//    public function testInsert()
//    {
//        $result = $this->postgres->insert("INSERT INTO public.user (email, password, deleted)
//                                  VALUES ('waaffles@gmail.com','wordpass', false)", array());
//        $this->assertInternalType('array', $result);
//        if (is_string($result)) {
//            $this->assertEquals($result, "Query did not return any results");
//        }
//    }
//
//    //Select on data entered to verify it is in there
//    public function testSelect()
//    {
//        //Returns track ids that belong to the same album
//        $result = $this->postgres->select("SELECT * FROM PUBLIC.user WHERE username = 'waaffles'", array());
//        //var_dump($result);
//        //TODO process result or send back status code
//        foreach($result as $key => $results) {
//            foreach($results as $field => $value) {
//                $this->assertInternalType('array', $results);
//                if ($value != NULL) {
//                    $this->assertInternalType('string', $value);
//                    $this->assertTrue(is_int(intval($results['id'])));
//                    $this->assertTrue($results['username'] == 'waaffles');
//                    $this->assertTrue($results['email'] == 'waaffles@gmail.com');
//                    $this->assertTrue($results['password'] == 'wordpass');
//                    $this->assertTrue($results['password_confirmation'] == 'wordpass');
//                }
//            }
//        }
//    }
//
// //   Will alter column username, $result will be false even though it has no data to return from the successful alter
//    public function testAlter()
//    {
//        $result = $this->postgres->execute("ALTER TABLE PUBLIC.user RENAME COLUMN username TO user_name", array());
//        if (is_string($result)) {
//            $this->assertEquals($result, "Query did not return any results");
//        }
//    }
//
//    public function testUpdate()
//    {
//        $result = $this->postgres->execute("update public.user SET email = 'davida@gmail.com' WHERE id = 2;", array());
//    }
//
//    //Will Select the same query, but check that the column has changed
//    public function testSelectAfterAlter()
//    {
//        //Returns track ids that belong to the same album
//        $result = $this->postgres->select("SELECT * FROM PUBLIC.user WHERE user_name = 'waaffles'", array());
//        //var_dump($result);
//        //TODO process result or send back status code
//        foreach($result as $key => $results) {
//            foreach($results as $field => $value) {
//                $this->assertInternalType('array', $results);
//                if ($value != NULL) {
//                    $this->assertInternalType('string', $value);
//                    $this->assertTrue(is_int(intval($results['id'])));
//                    $this->assertTrue($results['user_name'] == 'waaffles'); //this is checking for the altered column_name
//                    $this->assertTrue($results['email'] == 'waaffles@gmail.com');
//                    $this->assertTrue($results['password'] == 'wordpass');
//                    $this->assertTrue($results['password_confirmation'] == 'wordpass');
//                }
//            }
//        }
//    }
//
//    //Reverts column changed back to original
//    public function testAlter2()
//    {
//        $result = $this->postgres->execute("ALTER TABLE PUBLIC.user RENAME COLUMN user_name TO username");
//        if (is_string($result)) {
//            $this->assertEquals($result, "Query did not return any results");
//        }
//    }
//
//    //Deletes the data that we initially entered
//    public function testDelete()
//    {
//        $result = $this->postgres->delete("DELETE FROM PUBLIC.user WHERE username = 'waaffles'", array());
//        if(is_string($result)) {
//            $this->assertEquals($result, "Query did not return any results");
//        } else {
//            $this->assertInternalType('array', $result);
//        }
//    }
//
//    //Does a select on the data we just deleted, which should return false since there is no data to be returned.
//    public function testErrorResult()
//    {
//        $result = $this->postgres->execute("SELECT * FROM PUBLIC.user WHERE username = 'waaffles'", array());
//        $this->assertEquals($result, "Query did not return any results");
//    }
//
//    public function testUpdateQuery()
//    {
//        $clientData = array(
//            "service" => "GroupService",
//            "request" => "updateArtistProfile",
//            "operation" => "update",
//            "limit" => "20",
//            "priority" => 10,
//            "data" => array(
//                "group_profile" => array(
//                    "name" => "lollipop",
//                    "bio" => "bio?",
//                    "year_founded" => 2003,
//                    "website" => "",
//                    "genre" => "",
//                    "group_id" => 2,
//                    "location" => "",
//                )
//            )
//        );
//        $this->databaseService->setUpQuery($clientData);
//        $this->assertEquals(
//            array("0" => array("data" => "i am data from the database")),
//            $this->databaseService->sendToDB()
//        );
//    }
//}
