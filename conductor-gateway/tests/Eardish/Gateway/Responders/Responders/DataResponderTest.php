<?php
//namespace Eardish\Gateway\Responders\Responders;
//
//use Eardish\DataObjects\Blocks\DataBlock;
//use Eardish\DataObjects\Blocks\MetaBlock;
//use Eardish\DataObjects\Response;
//
//class DataResponderTest extends \PHPUnit_Framework_TestCase
//{
//    protected $object;
//
//    protected function setUp()
//    {
//        $arr = array(
//            "model" => "loggedInUser",
//            "raw" => array("name" => "TestName", "path" => "artists"),
//            "action" => "null",
//            "communicationType" => "[requested|update]"
//        );
//        $statusCode = 32;
//
//        $data = new DataBlock(array(
//            'profile' => array('name' => 'Bonjovi', 'location' => 'Sayreville, New Jersey', 'genre' => 'rock'),
//            'albums' => array('id' => 524, 'name' => "Bon Jovi")
//        ));
//        $meta = new MetaBlock();
//        $this->object = new DataResponder($statusCode, $arr, new Response(array($data, $meta)));
//    }
//    public function testStatus()
//    {
//        $this->assertInstanceOf('Eardish\Gateway\DataObjects\DataObjects\DataBlock', $this->object->getBlock("data"));
//    }
//    public function testGetFull()
//    {
//        $export = $this->object->getFull();
//        $this->assertEquals(
//            '{"status":{"code":32,"message":"A third-party service refused the service call and the reason is described."},"data":{"model":"loggedInUser","raw":{"name":"TestName","path":"artists"},"action":"null","communicationType":"[requested|update]"}}',
//            json_encode($export));
//    }
//}