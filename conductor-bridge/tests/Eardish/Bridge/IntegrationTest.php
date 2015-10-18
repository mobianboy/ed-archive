<?php
namespace Eardish\Bridge;

use Eardish\EphectDataObjects\DataObjects\Request;
use Eardish\EphectDataObjects\DataObjects\RequestObjects\ActionBlock;
use Eardish\EphectDataObjects\DataObjects\RequestObjects\AudioBlock;
use Eardish\EphectDataObjects\DataObjects\RequestObjects\AuditBlock;
use Eardish\EphectDataObjects\DataObjects\RequestObjects\AuthBlock;
use Eardish\EphectDataObjects\DataObjects\RequestObjects\HeadBlock;
use Eardish\EphectDataObjects\DataObjects\RequestObjects\RouteBlock;
use Eardish\Bridge\Agents\Core\Connection;
use Eardish\Bridge\BridgeKernel;

/**
 * This test assumes all the Agents sockets are open, the Group Service is running and the DB service is running.
 *
 * Class IntegrationTest
 * @package Eardish\Bridge
 */
class IntegrationTest
{
//    extends \PHPUnit_Framework_TestCase
//{
//    /**
//     * @var Request
//     */
//    protected $dto;
//
//    protected $connection;
//
//    /**
//     * @var ServiceManager
//     */
//    protected $manager;
//
//    public function setUp()
//    {
//        $this->dto = new Request(new ActionBlock("/Group/getGroupTracks", "10"), new ComponentBlock("12345"),
//            new HeadBlock("http", 11), new AuthBlock("123456789", "password"), new AudioBlock("Wilco", "Handshake Drugs", "free", "today", 63342312, "yes", "MP3"), new RouteBlock(), new AuditBlock());
//        $this->connection = new ConnectionAgent();
//        $this->dto->getRoute()->setAction("getGroupTracks");
//        $this->dto->getRoute()->setController("Group");
//
//        $this->manager = new ServiceManager($this->connection);
//    }
//
//    public function testGetGroupTracks()
//    {
//        $result = $this->manager->inbound($this->dto);
//        var_dump($result);
//        $this->assertEquals(
//            '',
//            $result
//        );
//    }
}