<?php
namespace Eardish\Gateway\Socket;

class ConnectionTest extends \PHPUnit_Framework_TestCase
{
    protected $conn;

    public function setUp()
    {
        $reflection = new \ReflectionClass('\\Eardish\\Gateway\\Socket\\Connection');
        $this->conn = $reflection->newInstanceWithoutConstructor();
    }

    public function testUpgrade()
    {
        $this->assertFalse(
            $this->conn->isUpgraded()
        );

        $this->conn->upgrade();
        $this->assertTrue(
            $this->conn->isUpgraded()
        );
    }

    public function testGetSetResourceId()
    {
        $this->conn->setResourceId(5);
        $this->assertEquals(
            5,
            $this->conn->getResourceId()
        );
    }
}