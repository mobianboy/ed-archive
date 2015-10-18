<?php
//namespace Eardish\DatabaseService;
//
//use Eardish\DatabaseService\DatabaseControllers\ElasticController;
//
//class ElasticControllerTest extends \PHPUnit_Framework_TestCase
//{
//    /**
//     * @var ElasticController
//     */
//    protected $controller;
//
//    public function setUp()
//    {
//        $this->controller = new ElasticController();
//    }
//
//    public function testSearch()
//    {
//        $jsonQuery = ('{ "fields" : ["username"] }');
//        $queryResult = $this->controller->select(json_decode($jsonQuery), 'user');
//        $this->assertInternalType('array', $queryResult);
//        $this->assertEquals($queryResult['_index'], 'eardish');
//        $this->assertEquals($queryResult['_type'], 'user');
//        $this->assertEquals($queryResult['fields']['username']['0'], 'kLemke');
//    }
//}
