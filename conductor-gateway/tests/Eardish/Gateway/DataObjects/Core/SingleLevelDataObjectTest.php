<?php

namespace Eardish\Gateway\DataObjects\Core;

use Eardish\Gateway\Formats\Factories\JSONFactory;

class SingleLevelDataObjectTest extends \PHPUnit_Framework_TestCase
{
    
    protected $object;
    
    protected function setUp()
    {
        $structure = array("code" => true,"message" => true);
        
        $this->object = new SingleLevelDataObject($structure, "status");
    }
    
    public function testStructure()
    {
        $structure = array("code" => true,"message" => true);
        
        $this->assertEquals(
                $structure,
                $this->object->getStructure()
                );
    }
    
    public function testOptions()
    {
        $options = array(
            "code" => "01",
            "message" => "test"
        );
        
        $this->object->setOptions($options);
        
        $this->assertEquals(
                $options,
                $this->object->getOptions()
                );
        
        $this->assertEquals(
                "01",
                $this->object->getOption("code")
                );
        
        $this->assertEquals(
                "test",
                $this->object->getOption("message")
                );
        
        $this->object->setOption("code", "10");
        
        $this->assertEquals(
                "10",
                $this->object->getOption("code")
                );
    }
    
    public function testOptionSettable()
    {
        $this->assertFalse(
                $this->object->isOptionSettable("notakey")
                );
        
        $this->assertTrue(
                $this->object->isOptionSettable("code")
                );
    }
    
    // Apologies for the magic methods
    public function testCallMagic()
    {
        $this->assertEquals(
                '',
                $this->object->getCode()
                );
        
        $this->object->setOption("code", "15");
        
        $this->assertEquals(
                "15",
                $this->object->getCode()
                );
        
        $this->object->setCode("20");
        
        $this->assertEquals(
                "20",
                $this->object->getCode()
                );
    }
    
    public function testSetOptionsException()
    {
        $this->setExpectedException('InvalidArgumentException');
        
        $this->object->setOptions("string not array");
    }
    
    public function testCallGetMethodException()
    {
        $this->setExpectedException('BadMethodCallException');
        
        $this->object->getNotAnOption();
    }
    
    public function testCallSetMethodException()
    {
        $this->setExpectedException('BadMethodCallException');
        
        $this->object->setNotAnOption("doesn't exist");
    }
    
    public function testCallUnknownMethodException()
    {
        $this->setExpectedException('BadMethodCallException');
        
        $this->object->aMethodThatIsUndefined();
    }
    
    public function testSetOptionException()
    {
        $this->setExpectedException('InvalidArgumentException');
        
        $this->object->setOption(array('code'), 'val');
    }
    
    public function testLoadStructure()
    {
        $this->assertEquals(
                array(),
                $this->object->loadStructure()
                );
        
        $this->assertEquals(
                array(
                    "code" => "This is a two digit code describing the response type.",
                    "message" => "This is an understandable message that accompanies the response code."
                ),
                $this->object->loadStructure("StatusCode")
                );
        
        $this->assertEquals(
                array(),
                $this->object->loadStructure("NonExistent")
                );
    }
    
}