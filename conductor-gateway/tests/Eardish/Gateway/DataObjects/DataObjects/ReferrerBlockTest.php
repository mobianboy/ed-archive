<?php

namespace Eardish\Gateway\DataObjects\DataObjects;

use Eardish\Gateway\Formats\Factories\JSONFactory;

class ReferrerBlockTest extends \PHPUnit_Framework_TestCase
{
    protected $obj;
    
    protected function setUp()
    {
        $this->obj = new ReferrerBlock();
    }
    
    public function testStructure()
    {
        $structure = array(
            "type" => "http, image, etc",
            "url" => "location of resource",
            "format" => "what format? API, Image or Audio"
        );
        
        $this->assertEquals(
                $structure,
                $this->obj->getStructure());
    }
    public function testSetReferrer()
    {
        $options = array(
            "type" => "http, image, etc",
            "url" => "location of resource",
            "format" => "what format? API, Image or Audio"
        );
        $this->obj->setReferrer($options);
        $this->assertEquals(
                'location of resource',
                $this->obj->getOption('url'));
    }
    public function testSetReferrerException()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->obj->setReferrer("This is not an array!");
    }
}
