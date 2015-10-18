<?php
namespace Eardish\EchoService;

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
        $this->connection = $this->getMockBuilder("Eardish\\EchoService\\Core\\Connection")->getMock();
        $logger = new Logger("service");

        $this->serviceKernel = new ServiceKernel(new EchoService($this->connection), $logger);
    }

    public function testSend()
    {
        // pass some data into the serviceKernel, get back the result
        $str =
            '{
                "method": "passEcho",
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
