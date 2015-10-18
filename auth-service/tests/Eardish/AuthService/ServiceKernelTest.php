<?php
namespace Eardish\AuthService;

use Eardish\AppConfig;
use Monolog\Logger;
use Eardish\AuthService\Core\Connection;


class ServiceKernelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ServiceKernel
     */
    protected $serviceKernel;
    protected $config;

    /**
     * @var Connection
     */
    protected $connection;

    public function setUp()
    {
        $this->connection = $this->getMockBuilder("Eardish\\AuthService\\Core\\Connection")->getMock();
        $logger = new Logger("service");
        $config = new AppConfig('/eda/secret/app.json', 'local');
        $this->serviceKernel = new ServiceKernel(new AuthService($this->connection, $config), $logger);
        // Mock the serviceAPI
    }

    public function testExecute()
    {
        // pass some data into the serviceKernel, get back the result

        $str = '{
            "method": "updatePassword",
            "priority": "10",
            "params": {
                "email": "email@eardish.com",
                "password": "password2"
            }
        }';

        $this->connection->method('sendToDB')->will($this->onConsecutiveCalls(
            [
                "success" => true,
                "password" => 'password2'
            ]
        ));

        $this->assertEquals(
            ["password" => 'password2'],
            $this->serviceKernel->execute($str)
        );
    }
}
