<?php

namespace Eardish\Gateway\Responders\Responders;

class MessageResponderTest extends \PHPUnit_Framework_TestCase
{
    protected $object;

    protected function setUp()
    {
        $arr = array("from"=>"val1", "type"=>"val2", "content"=>"val3", "destination"=>"val4");
        $this->object = new MessageResponder(1, $arr);
    }
    public function testStatus()
    {
        $this->assertInstanceOf('Eardish\Gateway\DataObjects\DataObjects\MessageBlock', $this->object->getBlock("message"));
    }
    /*
    public function testGetFull()
    {
        $export = $this->object->getFull();
        $this->assertEquals(
            '{"message":{"from":"val1","type":"val2","content":"val3","destination":"val4"}}',
            json_encode($export));
    }
    */
}
