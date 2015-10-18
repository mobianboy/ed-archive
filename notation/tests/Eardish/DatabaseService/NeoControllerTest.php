<?php
//namespace Eardish\DatabaseService;
//
//use Eardish\DatabaseService\DatabaseControllers\NeoController;
//use Everyman\Neo4j\PropertyContainer;
//
//class NeoControllerTest extends \PHPUnit_Framework_TestCase
//{
//    /**
//     * @var NeoController
//     */
//    protected $controller;
//
//    public function setUp()
//    {
//        $this->controller = new NeoController();
//    }
//
//    public function testSelect()
//    {
//        $results = $this->controller->select("MATCH (n:`User`) RETURN n.username LIMIT 10");
//        $this->assertInternalType('array',$results);
//        $this->assertContainsOnly('string', $results);
//        $this->assertTrue(sizeof($results)== 10);
//
//        $results = $this->controller->select("MATCH (n:`User`) RETURN n LIMIT 3");
//
//        if (is_array($results[0])) {
//            foreach($results as $index => $properties) {
//                $this->assertInternalType('array',$properties);
//                $this->assertArrayHasKey('id', $properties);
//                $this->assertArrayHasKey('username', $properties);
//                    foreach($properties as $property => $value) {
//                        $this->assertTrue(is_string($property));
//                        $this->assertTrue(is_string($value));
//                    }
//            }
//        }
//    }
//}
