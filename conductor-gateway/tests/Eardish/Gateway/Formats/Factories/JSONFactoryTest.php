<?php

namespace Eardish\Gateway\Formats\Factories;

class JSONFactoryTest extends \PHPUnit_Framework_TestCase
{
    
    protected $object;
    
    protected function setUp()
    {
        $this->object = new JSONFactory();
    }
    
    public function testBuildPartialExport()
    {
        // Test array input
        $this->assertEquals(
                '"testElem": {"testSub1":"testval","testSub2":"testval"}',
                $this->object->buildPartialExport("testElem", array("testSub1" => "testval", "testSub2" => "testval"))
                );
        // Test string input
        $this->assertEquals(
                '"testElem": "testval"',
                $this->object->buildPartialExport("testElem", "testval")
                );
        // Test int input
        $this->assertEquals(
                '"testElem": 5',
                $this->object->buildPartialExport("testElem", 5)
                );
    }
    
    public function testBuildSingleExportInt()
    {
        // Test int input
        $this->assertEquals(
                '"testElem": 5',
                $this->object->buildSingleExport("testElem", 5)
                );
        // Test string input
        $this->assertEquals(
                '"testElem": "testval"',
                $this->object->buildSingleExport("testElem", "testval")
                );
    }
    
    public function testHeaders()
    {
        // Test array headers
        $obj1 = new JSONFactory(array("test1" => "val", "test2" => "val"));
        
        $string1 = '"headers": {"test1":"val","test2":"val"},'.PHP_EOL;
        
        $this->assertEquals(
                $string1,
                $obj1->headers()
                );
        
        // Test string header
        $obj2 = new JSONFactory("header val");
        
        $string2 = '"headers": "header val",'.PHP_EOL;
        
        $this->assertEquals(
                $string2,
                $obj2->headers()
                );
    }
    
    public function testBuildFullExport()
    {
        $obj = new JSONFactory("test");
        
        $testArray = array(
            "test1" => "val1",
            "test2" => array(
                "test3" => "val3"
            )
        );
        
        $string1 = "{".PHP_EOL
                .'"headers": "test",'.PHP_EOL
                .'"test1": "val1",'.PHP_EOL
                .'"test2": {"test3":"val3"}'.PHP_EOL
                .'}';
        
        $this->assertEquals(
                $string1,
                $obj->buildFullExport($testArray)
                );
        
        $string2 = "{".PHP_EOL
                .'"test1": "val1",'.PHP_EOL
                .'"test2": {"test3":"val3"}'.PHP_EOL
                .'}';
        
        $this->assertEquals(
                $string2,
                $this->object->buildFullExport($testArray)
                );
        
    }
}