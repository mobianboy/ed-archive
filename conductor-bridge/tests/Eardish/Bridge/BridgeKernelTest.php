<?php
namespace Eardish\Bridge;

use Eardish\DataObjects\Request;
use Eardish\DataObjects\Blocks\ActionBlock;
use Eardish\DataObjects\Blocks\AuditBlock;
use Eardish\DataObjects\Blocks\AuthBlock;
use Eardish\DataObjects\Blocks\DataBlock;
use Eardish\DataObjects\Blocks\MetaBlock;
use Eardish\DataObjects\Blocks\RouteBlock;
use Eardish\Bridge\Config\JSONLoader;
use Eardish\AppConfig;

class BridgeKernelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BridgeKernel
     */
    protected $kernel;
    protected $connection;
    protected $dto;

    public function setUp()
    {
        $this->dto = new Request(array(new ActionBlock("/Group/getFollowers", "10"), new DataBlock(array("password" => "i am a bio")),
            new MetaBlock("http", 11), new AuthBlock("123456789", "password"), new RouteBlock(), new AuditBlock()));

        $this->dto->getRouteBlock()->setControllerMethod("newUser");
        $this->dto->getRouteBlock()->setControllerName("User");
        $this->connection = $this->getMockBuilder("Eardish\\Bridge\\Agents\\Core\\Connection")
            ->getMock();


        $this->connection->method('start')->willReturn("test");

        $config = new AppConfig('app.json', 'local');

        $this->kernel = $this->getMockBuilder("Eardish\\Bridge\\BridgeKernel")
            ->setConstructorArgs(array($this->connection, $config))
            ->setMethods(array('outbound'))
            ->getMock();

        $this->dto->getDataBlock()->setDataArray(
            array(
                'password' => 'eardishrocks',
                'passwordConfirmation' => 'eardishrocks',
                'email' => 'test@eardish.com',
                'name' => array(
                    'first' => 'senor',
                    'last' => 'dish',
                ),
                'yearOfBirth' => '1965',
                'zipcode' => '95607',
                'invite-code' => 'k6g77d3'
            )
        );
    }

    public function testDIC()
    {
        /*
         * Test that an actual instance of UserController is actually accessible via the DIC
         */
        $this->assertInstanceOf(
            'Eardish\\Bridge\\Controllers\\UserController',
            $this->kernel->getDic()->get('Eardish\\Bridge\\Controllers\\UserController')
        );


        /*
         * Test that both UserController and GroupController got the same instance of photoAgent.
         */
        $this->assertEquals(
            $this->kernel->getDic()->params['Eardish\\Bridge\\Controllers\\UserController']['imageProcessingAgent'],
            $this->kernel->getDic()->params['Eardish\\Bridge\\Controllers\\ProfileController']['imageProcessingAgent']
        );

        /*
         * Check to see if the same instance of the photoAgent is being passed around
         */
    }

    public function testMarshal()
    {
        $data = base64_encode(serialize($this->dto));

        $this->assertEquals(
            unserialize(base64_decode($data)),
            $this->kernel->unserialize($data)
        );
    }

    public function testSendServiceResult()
    {
        $socketMock = $this->getMockBuilder('\Eardish\Bridge\Agents\Core\SocketAgent',
            array('sendToAPI'))
            ->setMethods(array('sendToAPI'))
            ->disableOriginalConstructor()
            ->getMock();

        $socketMock->method('sendToAPI')
            ->with("result")
            ->willReturn("0");

        $this->assertEquals(
            "0",
            $this->kernel->sendServiceResult("result", $socketMock)
        );
    }

    public function testJsonLoader()
    {
        $json = new JSONLoader();
        // assert an invalid json file path returns an empty array
        $this->assertEquals(
            array(),
            $json->loadJSONConfig('')
        );
    }
}
