<?php
namespace Eardish\Gateway;

use Monolog\Logger;

class GatewayKernelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var GatewayKernel
     */
    protected $mgr;
    /**
     * @var Agents\Core\Connection
     */
    protected $conn;
    public function setUp()
    {
        $configFile = realpath(dirname(__DIR__). '/../..') . '/app.json';

        $config = new \Eardish\AppConfig($configFile, "local");

        $log = new Logger("test");
        $this->mgr = new GatewayKernel($log, $config);
    }
    public function getNewConnection($i = 1)
    {
        $conn = $this->getMockBuilder('Eardish\\Gateway\\Socket\\Connection')
            ->setMethods(array("end"))
            ->disableOriginalConstructor()
            ->getMock();

        $conn->setResourceId($i);
        return $conn;
    }
    public function testNewConnection()
    {
        $connResult = $this->mgr->newConnection($this->getNewConnection());

        $this->assertEquals(
            1,
            $connResult
        );
    }
    public function testKillConnectionSoft()
    {
        $mgr = $this->mgr;
        $mgr->newConnection($this->getNewConnection(2));
        $mgr->setConnRoute(2, "/artist/new")->setConnUser(2, 12);
        $this->assertInstanceOf(
            'Eardish\Gateway\GatewayKernel',
            $mgr->killConnection(2)
        );
    }
    public function testKillConnectionHard()
    {
        $conn = $this->getNewConnection(3);

        $mgr = $this->mgr;
        $mgr->newConnection($conn);
        $mgr->setConnRoute(3, "/artist/new")->setConnUser(3, 12);
        $this->assertInstanceOf(
            'Eardish\Gateway\GatewayKernel',
            $mgr->killConnection(3, $conn)
        );
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionCode 21
     * @expectedExceptionMessage GATEWAY::JSON cannot be decoded
     */
    public function testExceptionBadJSON()
    {
        $mgr = $this->mgr;
        $data = "{'bad'',json";
        $mgr->handle($data, null);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionCode 41
     */

    /*
    public function testExceptionInvalidAuthData()
    {
        $mgr = $this->mgr;
        $data = '{"email":"rb@blah.bla"}';  // , "password":"fart"}';
        $mgr->handle($data, null);
    }
*/


//    public function testHandleSuccessfulAuth()
//    {
//        $conn = $this->getMockBuilder('Eardish\Gateway\Containers\Core\ConnectionContainer')
//            ->getMock();
//        $conn->expects($this->once())
//            ->method('send')
//            ->with(
//                "authenticate|gooduser::goodpass"
//            )->willReturn(
//                "valid|12"
//            );
//        $mgr = $this->mgr;
//        $mgr->setConnectionContainer($conn);
//        $data = array(
//            "action" => array(
//                "route" => "/test/route",
//                "priority" => "low"
//            ),
//            "component" => array(
//                "profile_id" => 12
//            ),
//            "head" => array(
//                "http" => "test"
//            ),
//            "auth" => array(
//                "user" => "gooduser",
//                "pass" => "goodpass"
//            )
//        );
//        $dataJSON = json_encode($data, JSON_FORCE_OBJECT);
//        $mgr->newConnection($this->getNewConnection(5));
//        $this->assertTrue(
//            $mgr->handle($dataJSON, 5)
//        );
//    }
//    public function testHandleBadAuth()
//    {
//        $conn = $this->getMockBuilder('Eardish\Gateway\Containers\Core\ConnectionContainer')
//            ->getMock();
//        $conn->expects($this->once())
//            ->method('send')
//            ->with(
//                "authenticate|baduser::badpass"
//            )->willReturn(
//                "invalid|0"
//            );
//        $mgr = $this->mgr;
//        $mgr->setConnectionContainer($conn);
//        $data = array(
//            "action" => array(
//                "route" => "/test/route",
//                "priority" => "low"
//            ),
//            "component" => array(
//                "profile_id" => 12
//            ),
//            "head" => array(
//                "http" => "test"
//            ),
//            "auth" => array(
//                "user" => "baduser",
//                "pass" => "badpass"
//            )
//        );
//        $dataJSON = json_encode($data, JSON_FORCE_OBJECT);
//        $mgr->newConnection($this->getNewConnection(6));
//        $this->assertTrue(
//                $mgr->handle($dataJSON, 6)
//            );
//        }

}