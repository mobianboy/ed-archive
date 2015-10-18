<?php
namespace Eardish\DataObjects\Blocks;

class RouteBlockTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RouteBlock
     */
    protected $object;

    public function setUp()
    {
        $this->object = new RouteBlock();
    }

    public function testSetGetController()
    {
        $this->object->setControllerName("Echo");

        $this->assertEquals(
            "Echo",
            $this->object->getControllerName()
        );
    }

    public function testGetSetAction()
    {
        $this->object->setControllerMethod("reverse");

        $this->assertEquals(
            "reverse",
            $this->object->getControllerMethod()
        );
    }
}
