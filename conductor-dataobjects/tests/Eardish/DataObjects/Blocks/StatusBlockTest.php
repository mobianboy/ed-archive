<?php
namespace Eardish\DataObjects\Blocks;

class StatusBlockTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var StatusBlock
     */
    protected $object;

    public function setUp()
    {
        $this->object = new StatusBlock('21', 'success');
    }

    public function testGetSetCode()
    {
        $this->assertEquals(
            '21',
            $this->object->getCode()
        );

        $this->object->setCode('40');
        $this->assertEquals(
            '40',
            $this->object->getCode()
        );
    }

    public function testGetSetMessage()
    {
        $this->assertEquals(
            'success',
            $this->object->getMessage()
        );

        $this->object->setMessage('null responder');
        $this->assertEquals(
            'null responder',
            $this->object->getMessage()
        );
    }
}