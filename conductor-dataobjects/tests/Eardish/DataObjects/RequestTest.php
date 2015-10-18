<?php
namespace Eardish\DataObjects;

//changes in use statements affect json blocks in ClientData, RequestObjects in DataObjects repo, APIInterpreter and APIInterpreterTest in ephect-api repo,
// and Request.php
use Eardish\DataObjects\Blocks\ActionBlock;
use Eardish\DataObjects\Blocks\AuditBlock;
use Eardish\DataObjects\Blocks\AuthBlock;
use Eardish\DataObjects\Blocks\DataBlock;
use Eardish\DataObjects\Blocks\MetaBlock;
use Eardish\DataObjects\Blocks\RouteBlock;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Request
     */
    protected $req;

    public function setUp()
    {
        $this->req = new Request(array(new ActionBlock("/Echo/pass", "10"), new DataBlock(array("bio" => "we are a band")),
            new MetaBlock("http", "12345"), new AuthBlock("123456789", "password"), new RouteBlock(),
            new AuditBlock()));
    }

    public function testGetAuth()
    {
        $this->assertInstanceOf(
            "Eardish\\DataObjects\\Blocks\\AuthBlock",
            $this->req->getAuthBlock()
        );
    }

    public function testGetComponent()
    {
        $this->assertInstanceOf(
            "Eardish\\DataObjects\\Blocks\\DataBlock",
            $this->req->getDataBlock()
        );
    }

    public function testGetMeta()
    {
        $this->assertInstanceOf(
            "Eardish\\DataObjects\\Blocks\\MetaBlock",
            $this->req->getMetaBlock()
        );
    }

    public function testGetRoute()
    {
        $this->assertInstanceOf(
            "Eardish\\DataObjects\\Blocks\\RouteBlock",
            $this->req->getRouteBlock()
        );
    }

    public function testIsRouteable()
    {
        $this->assertEquals(
            false,
            $this->req->isRouteable()
        );

        $this->req->getRouteBlock()->setControllerMethod('User');
        $this->req->getRouteBlock()->setControllerName('newUser');

        $this->assertEquals(
            true,
            $this->req->isRouteable()
        );

    }

    public function testSetAudit()
    {
        $this->req->injectBlock(new AuditBlock());

        $this->assertInstanceOf(
            "Eardish\\DataObjects\\Blocks\\AuditBlock",
            $this->req->getAuditBlock()
        );
    }

    public function testJsonEncode()
    {
        $this->req->addException(new \Exception("failed"));

        $this->assertEquals(
            '{"action":{"route":"\/Echo\/pass","priority":"10"},"data":{"bio":"we are a band"},"meta":[],"auth":{"email":"123456789","password":"password"},"route":{"is-routeable":false},"audit":[]}',
            json_encode($this->req)
        );
    }
}
