<?php
namespace Eardish\AnalyticsService;

use Eardish\AppConfig;
use Monolog\Logger;
use Eardish\AnalyticsService\Core\Connection;


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
        $this->connection = $this->getMockBuilder("Eardish\\AnalyticsService\\Core\\Connection")->getMock();
        $logger = new Logger("service");
        $config = new AppConfig('/eda/secret/app.json', 'local');
        $this->serviceKernel = new ServiceKernel(new AnalyticsService($this->connection, $config), $logger);
        // Mock the serviceAPI
    }

    public function testExecute()
    {
        // pass some data into the serviceKernel, get back the result

        $str = '{
            "method": "getUserStats",
            "priority": "10",
            "params": {
                "userId": "203",
                "event": "completedListen"
                }
        }';

        $this->connection->method('sendToDB')
            ->willReturn(
                [
                    "data" => [
                        0 => [
                            'id' => 203]] ]
            );

        $this->assertEquals(
            [0 => ['id' => 203]],
            $this->serviceKernel->execute($str)
        );
    }
}
