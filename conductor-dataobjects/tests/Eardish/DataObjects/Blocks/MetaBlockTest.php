<?php
namespace Eardish\DataObjects\Blocks;

class MetaBlockTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MetaBlock
     */
    protected $object;

    public function setUp()
    {
        $this->object = new MetaBlock();
    }

    public function testGetConnId()
        // tests get http
    {
        $this->object->setConnId("12345");
        $this->assertEquals(
            "12345",
            $this->object->getConnId()
        );
    }

    public function testGetSetCurrentProfile()
    {
        $this->object->setCurrentProfile(3);
        $this->assertEquals(
            3,
            $this->object->getCurrentProfile()
        );
    }

    public function testGetSetCurrentUser()
    {
        $this->object->setCurrentUser(5);
        $this->assertEquals(
            5,
            $this->object->getCurrentUser()
        );
    }

    public function testGetSetApiVersion()
    {
        $this->object->setApiVersion('1.07');
        $this->assertEquals(
            '1.07',
            $this->object->getApiVersion()
        );
    }

    public function testGetSetResponseToken()
    {
        $this->object->setResponseToken(5630923492334429);
        $this->assertEquals(
            5630923492334429,
            $this->object->getResponseToken()
        );
    }

    public function testGetSetModelType()
    {
        $this->object->setModelType('song');
        $this->assertEquals(
            'song',
            $this->object->getModelType()
        );
    }

    public function testGetSetDataSource()
    {
        $this->object->setDataSource('postgres');
        $this->assertEquals(
            'postgres',
            $this->object->getDataSource()
        );
    }
}
