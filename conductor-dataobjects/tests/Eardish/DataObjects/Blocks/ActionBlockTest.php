<?php
namespace Eardish\DataObjects\Blocks;

class ActionBlockTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ActionBlock
     */
    protected $object;

    public function setUp()
    {
        $this->object = new ActionBlock("testRoute", 5);
    }

    public function testGetPriority()
        // tests get priority
    {
        $testPriority = $this->object->getPriority();

        $this->assertEquals(
            $testPriority,
            5);
    }

    public function testSetPriority()
        // tests set priority
    {
        $this->object->setPriority(7);

        $this->assertEquals(
            $this->object->getPriority(),
            7
        );
    }

    public function testGetRoute()
        // tests get route
    {
        $testRoute = $this->object->getRoute();

        $this->assertEquals(
            $testRoute,
            "testRoute");
    }

    public function testSetRoute()
        // tests set route
    {
        $this->object->setRoute("anotherRoute");

        $this->assertEquals(
            $this->object->getRoute(),
            "anotherRoute");
    }

    public function testGetSetResponseToken()
    {
        $this->object->setResponseToken(5630923492334429);
        $this->assertEquals(
            5630923492334429,
            $this->object->getResponseToken()
        );
    }
}
