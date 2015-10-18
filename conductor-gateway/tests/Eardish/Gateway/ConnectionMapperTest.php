<?php
namespace Eardish\Gateway;

class ConnectionMapperTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }
    public function getNewConnection($i = 1)
    {
        $conn = $this->getMockBuilder('Eardish\\Gateway\\Socket\\Connection')
            ->setMethods(array("on"))
            ->disableOriginalConstructor()
            ->getMock();


        $conn->setResourceId($i);
        return $conn;
    }
    public function testConnect()
    {
        $conn = $this->getNewConnection();
        $connMapper = new ConnectionMapper();
        $this->assertEquals(
            1,
            $connMapper->connect($conn)
        );
        $conns = $connMapper->getConnMap();
        $this->assertEquals(
            1,
            count($conns)
        );
        $this->assertInstanceOf(
            'Eardish\Gateway\Socket\Connection',
            $conns[1]
        );
    }
    public function testGetConnObj()
    {
        $connMapper = new ConnectionMapper();
        $connMapper->connect($this->getNewConnection());
        $this->assertInstanceOf(
            'Eardish\Gateway\Socket\Connection',
            $connMapper->getConnObj(1)
        );
        $this->assertNull(
            $connMapper->getConnObj(0)
        );
    }
    public function testGetConnObjs()
    {
        $connMapper = new ConnectionMapper();
        $connMapper->connect($this->getNewConnection(1));
        $connMapper->connect($this->getNewConnection(2));
        $ids = [1, 2];
        $result = $connMapper->getConnObjs($ids);
        $this->assertEquals(
            2,
            count($result)
        );
        $this->assertInstanceOf(
            'React\Socket\Connection',
            $result[0]
        );
    }
    public function testRouting()
    {
        $connMapper = new ConnectionMapper();
        $connMapper->connect($this->getNewConnection(1));
        $firstRoute = "/artist/new";
        $connMapper->updateRoute(1, $firstRoute);
        $conns = $connMapper->getByRoute($firstRoute);
        $this->assertEquals(
            1,
            count($conns)
        );
        $this->assertInstanceOf(
            'Eardish\Gateway\Socket\Connection',
            $conns[0]
        );
        $secondRoute = "/artist/1492";
        $connMapper->updateRoute(1, $secondRoute);
        $conns = $connMapper->getByRoute($secondRoute);
        $this->assertEquals(
            1,
            count($conns)
        );
        $this->assertInstanceOf(
            'Eardish\Gateway\Socket\Connection',
            $conns[0]
        );
    }
    public function testUser()
    {
        $connMapper = new ConnectionMapper();
        $connMapper->connect($this->getNewConnection(1));
        $this->assertFalse(
            $connMapper->getUserByConn(1)
        );
        $connMapper->setUser(1, 12);
        $this->assertEquals(
            12,
            $connMapper->getUserByConn(1)
        );
        $conns = $connMapper->getByUser(12);
        $this->assertEquals(
            1,
            count($conns)
        );
        $this->assertInstanceOf(
            'Eardish\Gateway\Socket\Connection',
            $conns[0]
        );
    }
    public function testDisconnect()
    {
        $connMapper = new ConnectionMapper();
        $connMapper->connect($this->getNewConnection(1));
        $connMapper->setUser(1, 12);
        $connMapper->updateRoute(1, "/artist/new");
        $connMapper->getConnObj(1)->upgrade();
        $connMapper->getConnObj(1)->setConnAuth();
        $connMapper->getConnObj(1)->setConnRoute();
        $this->assertEquals(
            12,
            $connMapper->getUserByConn(1)
        );
        $this->assertEquals(
            "/artist/new",
            $connMapper->getRouteByConn(1)
        );
        $connMapper->disconnect(1);
        $this->assertFalse(
            $connMapper->getUserByConn(1)
        );
        $this->assertFalse(
            $connMapper->getRouteByConn(1)
        );
    }
}