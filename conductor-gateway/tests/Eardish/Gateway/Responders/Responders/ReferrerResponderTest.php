<?php

namespace Eardish\Gateway\Responders\Responders;

class ReferrerResponderTest extends \PHPUnit_Framework_TestCase
{
    protected $object;
    
    protected function setUp()
    {
        $arr = array("test1"=>"val1", "test2"=>"val2");
        $statusCode = 10;
        $this->object = new ReferrerResponder($statusCode, $arr);
    }

    public function testStatus()
    {
        $this->assertInstanceOf('Eardish\Gateway\DataObjects\DataObjects\StatusCode', $this->object->getBlock("status"));
        $this->assertInstanceOf('Eardish\Gateway\DataObjects\DataObjects\ReferrerBlock', $this->object->getBlock("referrer"));
    }
    public function testGetFull()
    {
        $statusCode = 32;
        $arr = array(
            "type" => "http, image, etc",
            "url" => "location of resource",
            "format" => "what format? API, Image or Audio"
        );
        $this->object = new ReferrerResponder($statusCode, $arr);
        $export = $this->object->getFull();
        $this->assertEquals(
            '{"status":{"code":32,"message":"A third-party service refused the service call and the reason is described."},"referrer":{"type":"http, image, etc","url":"location of resource","format":"what format? API, Image or Audio"}}',
            json_encode($export));
    }
}