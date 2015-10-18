<?php

namespace Eardish\Gateway\Responders\Responders;

use Eardish\Gateway\Formats\Factories\JSONFactory;
use Eardish\Gateway\DataObjects\DataObjects\StatusCode;

class NullResponderTest extends \PHPUnit_Framework_TestCase
{
    protected $object;
    
    protected function setUp()
    {
        $statusCode = 10;
        $this->object = new NullResponder($statusCode);
    }
    
    /**/
    public function testIsCorrect()
    {
        $this->assertInstanceOf('Eardish\Gateway\DataObjects\DataObjects\StatusCode', $this->object->getBlock("status"));
    }
    public function testSetCode()
    {
        $status = new NullResponder(10);
        $this->assertEquals(
            10,
            $status->getBlock('status')->getOption('code')
        );
    }
    public function testGetFull()
    {
        $json = '{"status":{"code":10,"message":"No action could be found for the request."}}';
        $this->assertEquals(
            $json,
            json_encode($this->object->getFull()));
    }
    /**/
}
