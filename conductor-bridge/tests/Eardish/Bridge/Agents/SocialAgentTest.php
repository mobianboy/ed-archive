<?php
//namespace Eardish\Bridge\Agents;
//
//use Eardish\EphectDataObjects\DataObjects\Request;
//use Eardish\EphectDataObjects\DataObjects\RequestObjects\ActionBlock;
//use Eardish\EphectDataObjects\DataObjects\RequestObjects\AuthBlock;
//use Eardish\EphectDataObjects\DataObjects\RequestObjects\DataBlock;
//use Eardish\EphectDataObjects\DataObjects\RequestObjects\HeadBlock;
//use Eardish\EphectDataObjects\DataObjects\RequestObjects\RouteBlock;
//use Eardish\EphectDataObjects\DataObjects\RequestObjects\AuditBlock;
//use Eardish\EphectDataObjects\DataObjects\RequestObjects\AudioBlock;
//use Eardish\Bridge\Agents;
//use Eardish\Bridge\ServiceManager;
//
//class SocialAgentTest extends \PHPUnit_Framework_TestCase
//{
//    protected $connection;
//
//    /**
//     * @var SocialAgent
//     */
//    protected $Agent;
//
//    /**
//     * @var Request
//     */
//    protected $dto;
//
//    /**
//     * @var ServiceManager
//     */
//    protected $manager;
//
//    public function setUp()
//    {
//        $this->connection = $this->getMockBuilder("Eardish\\Bridge\\Agents\\Core\\Connection")
//            ->getMock();
//
//        $this->connection->method('send')->willReturn("test");
//
//
//        $this->dto = new Request(new ActionBlock("/Echo/pass", "10"), new DataBlock(array("bio" => "i am a bio")),
//            new HeadBlock("http", 11), new AuthBlock("123456789", "password"), new AudioBlock("Wilco", "Handshake Drugs", "free", "today", 63342312, "yes", "MP3"), new RouteBlock(), new AuditBlock());
//
//        $this->manager = $this->getMockBuilder("Eardish\\Bridge\\ServiceManager")
//            ->setConstructorArgs(array($this->connection))
//            ->setMethods(array('outbound'))
//            ->getMock();
//
//        $this->Agent = new SocialAgent($this->connection, $this->manager->getPriority());
//    }
//}
