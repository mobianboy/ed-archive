<?php
namespace Eardish\Gateway\DataObjects\DataObjects;

class StatusCodeTest extends \PHPUnit_Framework_TestCase
{
    protected $object;
    
    protected function setUp()
    {
        $this->object = new StatusCode();
    }
    
    public function testStructure()
    {
        $struct = array(
            "code" => "This is a two digit code describing the response type.",
            "message" => "This is an understandable message that accompanies the response code."
        );
        
        $this->assertEquals(
                $struct,
                $this->object->getStructure()
                );
    }
    
    public function testSetCode()
    {
        $this->object->setCode(10);
        
        $this->assertEquals(
                10,
                $this->object->getOption("code")
                );

    }
    
    public function testSetCodeException()
    {
        $this->setExpectedException('InvalidArgumentException');
        
        $this->object->setCode("string not int");
    }
}