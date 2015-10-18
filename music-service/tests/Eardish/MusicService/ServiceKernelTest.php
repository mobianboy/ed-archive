<?php
namespace Eardish\MusicService;

use Eardish\AppConfig;
use Monolog\Logger;

class ServiceKernelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ServiceKernel
     */
    protected $serviceKernel;

    protected $connection;

    public function setUp()
    {
        $this->connection = $this->getMockBuilder("Eardish\\MusicService\\Core\\Connection")->getMock();
        $logger = new Logger("service");

        $config = new AppConfig('/eda/secret/app.json', 'local');
        $this->serviceKernel = new ServiceKernel(new MusicService($this->connection, $config), $logger);
    }

    public function testExecute()
    {
        // pass some data into the serviceKernel, get back the result
        $str =
            '{
                "method": "rateTrack",
                "priority": "10",
                "params" : {
                    "profileId": 11,
                    "trackId": 7,
                    "rating": 4
                }
            }';

        $this->connection->method('sendToDB')
            ->willReturn(
                [
                    'success' => true,
                ]
            );

        $this->assertTrue(
            $this->serviceKernel->execute($str)['success']
        );
    }

}
