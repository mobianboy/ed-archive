<?php
namespace Eardish\EmailService;

use Eardish\AppConfig;
use Monolog\Logger;
use Eardish\EmailService\Core\Connection;

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
        $this->connection = $this->getMockBuilder("Eardish\\EmailService\\Core\\Connection")->getMock();
        $logger = new Logger("service");
        $config = new AppConfig('/eda/secret/app.json', 'local');
        $this->serviceKernel = new ServiceKernel(new EmailService($this->connection, $config), $logger);
        // Mock the serviceAPI
    }

    public function testExecute()
    {
        // pass some data into the serviceKernel, get back the result

        $str = '{
            "method": "sendInviteCode",
            "priority": "10",
            "params": {
                "emails": [
                    "devdnr@eardish.com"
                    ],
                "inviteCode": "789tyu",
                "name": "Eardish User"
                }
        }';

        $this->connection->method('sendToDB')
            ->willReturn(
                [
                    'data' => ['email' => true]]
            );

        $this->assertEquals(
            ['data' => ['email' => true]],
            $this->serviceKernel->execute($str)
        );
    }
}
