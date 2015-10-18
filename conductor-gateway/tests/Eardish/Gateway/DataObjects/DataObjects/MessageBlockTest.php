<?php
/**
 * Created by PhpStorm.
 * User: kaitlin
 * Date: 11/14/14
 * Time: 10:54 AM
 */

namespace Eardish\Gateway\DataObjects\DataObjects;

use Eardish\Gateway\DataObjects\DataObjects\MessageBlock;

class MessageBlockTest extends \PHPUnit_Framework_TestCase
{
    protected $object;

    public function setUp()
    {
        $this->object = new MessageBlock();
    }

    public function testSetMessage()
    {
        // testing when $options is an array
        $this->object->setMessage(array("from" => "val1", "type" => "val2", "content" => "val3", "destination" => "val4"));
        $value = $this->object->getOptions();

        $this->assertEquals(
            array("from" => "val1", "type" => "val2", "content" => "val3", "destination" => "val4"),
            $value
        );
    }

    /**
     * @expectedException Exception
     */
    public function testMessageException()
    {
        // testing when options is not an array
        $this->object->setMessage("Not an array");
    }
}

