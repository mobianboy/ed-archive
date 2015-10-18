<?php
namespace Eardish\UserService;

use Eardish\AppConfig;
use Monolog\Logger;

class ServiceKernelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ServiceKernel
     */
    protected $serviceKernel;
    protected $config;
    protected $connection;

    public function setUp()
    {
        $this->connection = $this->getMockBuilder("Eardish\\UserService\\Core\\Connection")->getMock();
        $logger = new Logger("service");

        $config = new AppConfig('/eda/secret/app.json', 'local');
        $this->serviceKernel = new ServiceKernel(new UserService($this->connection, $config), $logger);
        // Mock the serviceKernel
    }

    public function testExecute()
    {
        // pass some data into the serviceKernel, get back the result
        $str =
            '{
                "method": "createUserProfile",
                "priority": "10",
                "params" : {
                    "userId": 10,
                    "firstName": "Joe",
                    "lastName": "Shmoe",
                    "yearOfBirth": 1990,
                    "zipcode": "91604"
                }
            }';

        $this->connection->method('sendToDB')
            ->willReturn(
                [
                    'success' => true,
                    'count' => 1,
                    'data' => [
                        0 => [
                            'id' => 19
                        ]
                    ]
                ]
            );

        $this->assertEquals(
            [
                'profileID' => 19
            ],
            $this->serviceKernel->execute($str)
        );
    }

}
