<?php
namespace Eardish\DataObjects\Blocks;

class FollowUpBlockTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FollowUpBlock
     */
    protected $object;

    public function setUp()
    {
        $this->object = new FollowUpBlock('login', 'facebook.com', 'api');
    }

    public function testGetSetType()
    {
        $this->assertEquals(
            'login',
            $this->object->getType()
        );

        $this->object->setType('access contacts');
        $this->assertEquals(
            'access contacts',
            $this->object->getType()
        );
    }

    public function testGetSetUrl()
    {
        $this->assertEquals(
            'facebook.com',
            $this->object->getUrl()
        );

        $this->object->setUrl('facebook.com/api');
        $this->assertEquals(
            'facebook.com/api',
            $this->object->getUrl()
        );
    }

    public function testGetSetFormat()
    {
        $this->assertEquals(
            'api',
            $this->object->getFormat()
        );

        $this->object->setFormat('http');
        $this->assertEquals(
            'http',
            $this->object->getFormat()
        );
    }
}