<?php

namespace Eardish\Gateway\DataObjects\DataObjects;

use Eardish\Gateway\DataObjects\DataObjects\DataBlockBlock;

class DataBlockTest extends \PHPUnit_Framework_TestCase
{
    protected $obj;

    protected function setUp()
    {
        $this->obj = new DataBlock();
    }
    public function testSetData()
    {
        $options = array(
            "model" => "loggedInUser",
            "raw" => array("name" => "TestName", "path" => "artists"),
            "action" => "null",
            "communicationType" => "[requested|update]"
        );
        $this->obj->setData($options);
        $this->assertEquals(
            'loggedInUser',
            $this->obj->getOption('model'));

        $this->assertEquals(
            array("name" => "TestName", "path" => "artists"),
            $this->obj->getOption('raw'));
    }
    public function testSetDataException()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->obj->setData("This is not an array!");
    }
}
