<?php

namespace Eardish\Gateway\Formats\Factories;

class XMLFactoryTest extends \PHPUnit_Framework_TestCase
{
    
    protected $object;
    const IND = '    ';

    protected function setUp()
    {
        $this->object = new XMLFactory();
    }

    public function testBuildPartialExport()
    {
        $string1 = '<?xml version=\'1.0\' encoding=\'UTF-8\'?>'.PHP_EOL
                . '<root>'.PHP_EOL
                .self::IND. '<name>Joe</name>'.PHP_EOL
                . '</root>';
        // test single item array
        $this->assertEquals(
                $string1,
                $this->object->buildPartialExport("root", array("name"=>"Joe")));
        
        $string2 = '<?xml version=\'1.0\' encoding=\'UTF-8\'?>'.PHP_EOL
                . '<root>'.PHP_EOL
                .self::IND. '<number>50</number>'.PHP_EOL
                .self::IND. '<second>100</second>'.PHP_EOL
                .self::IND. '<name>Joe</name>'.PHP_EOL
                . '</root>';
        // test int array
        $this->assertEquals(
                $string2,
                $this->object->buildPartialExport("root", array("number"=>50, "second"=>100, "name"=>"Joe")));
        
        $string3 = '<?xml version=\'1.0\' encoding=\'UTF-8\'?>'.PHP_EOL
                . '<data>'.PHP_EOL
                .self::IND. '<number>75</number>'.PHP_EOL
                .self::IND. '<second>100</second>'.PHP_EOL
                .self::IND. '<name>Joe</name>'.PHP_EOL
                . '</data>';
        // test root element change
        $this->assertEquals(
                $string3,
                $this->object->buildPartialExport("data", array("number"=>75, "second"=>100, "name"=>"Joe")));
        
        $depthArray = array("number"=>200, "second"=>100, "deeper" => array("deeperplus"=>4, "anotheritem"=>"item"), "val"=>"test");
    }
    
    public function testTraverse(){

        $string5 = self::IND.'<123>2</123>'.PHP_EOL
                .self::IND. '<second>100</second>'.PHP_EOL
                .self::IND. '<name>Joe</name>'.PHP_EOL;

        $this->assertEquals(
                $string5,
                $this->object->traverse(array(123=>2, "second"=>100, "name"=>"Joe"), self::IND));
        
    }

    public function testBuildSingleExport()
    {
        // test string
        $string6 = "<name>Joe</name>";
        $this->assertEquals(
                $string6,
                $this->object->buildSingleExport("name", "Joe"));
        $string7 = "<number>100</number>";
        // test int
        $this->assertEquals(
                $string7,
                $this->object->buildSingleExport("number", 100));
    }
    
    public function testHeaders()
    {
        // Test array headers
        $obj1 = new XMLFactory("test");
        $string = self::IND."<headers>test</headers>";
        $this->assertEquals(
                $string,
                $obj1->headers());
        
        $string2 = self::IND.'<headers>'.PHP_EOL
                . self::IND.self::IND.'<attribute1>setting 1</attribute1>'.PHP_EOL
                . self::IND.self::IND.'<attribute2>setting 2</attribute2>'.PHP_EOL
                . self::IND.'</headers>';

        $obj2 = new XMLFactory(array("attribute1"=>"setting 1", "attribute2"=>"setting 2"));
        $this->assertEquals(
                $string2,
                $obj2->headers());

        $this->assertEquals(
            '',
            $this->object->headers());
    }
        
    public function testBuildFullExport()
    {
        $obj = new XMLFactory("test");
        
        $testArray = array(
            "test1" => "val1",
            "arr"=> array ("test2"=>"val2", "test3"=>"val3"));
        
            $string = '<?xml version=\'1.0\' encoding=\'UTF-8\'?>'.PHP_EOL
                . '<root>'.PHP_EOL
                .'    '. '<headers>test</headers>'.PHP_EOL
                .'    '. '<test1>val1</test1>'.PHP_EOL
                .'    '. '<arr>'.PHP_EOL
                .'        '. '<test2>val2</test2>'.PHP_EOL
                .'        '. '<test3>val3</test3>'.PHP_EOL
                .'    '. '</arr>'.PHP_EOL
                .'</root>';
        
        $this->assertEquals(
                $string,
                $obj->buildFullExport($testArray));
        
        $testArray1 = array(
            "test1" => "val1");
        $obj1 = new XMLFactory(array("attr1"=>"on", "attr2"=>"off"));
        
        $string8 = '<?xml version=\'1.0\' encoding=\'UTF-8\'?>'.PHP_EOL
                . '<root>'.PHP_EOL
                .self::IND.'<headers>'.PHP_EOL
                . self::IND.self::IND.'<attr1>on</attr1>'.PHP_EOL
                . self::IND.self::IND.'<attr2>off</attr2>'.PHP_EOL
                . self::IND.'</headers>'.PHP_EOL    
                .self::IND. '<test1>val1</test1>'.PHP_EOL
                . '</root>';
        
        $this->assertEquals(
                $string8,
                $obj1->buildFullExport($testArray1));
    }
}

