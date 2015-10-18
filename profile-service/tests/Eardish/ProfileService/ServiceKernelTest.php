<?php
namespace Eardish\ProfileService;

use Eardish\ProfileService\Core\Connection;
use Eardish\AppConfig;
use Monolog\Logger;

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
        $this->connection = $this->getMockBuilder("Eardish\\ProfileService\\Core\\Connection")->getMock();
        $logger = new Logger("service");
        $config = new AppConfig('/eda/secret/app.json', 'local');
        $this->serviceKernel = new ServiceKernel(new ProfileService($this->connection, $config), $logger);
        // Mock the serviceAPI
    }

    public function testExecute()
    {
        // pass some data into the serviceKernel, get back the result

        $str = '{
            "method": "createProfile",
            "priority": "10",
            "params": {
                "id": "203",
                "user_id": "102",
                "art_id": "1",
                "contact_id": "102",
                "onboarded": "true",
                "type": "fan",
                "first_name": "Person",
                "last_name": "Name",
                "artist_name": "PN",
                "year_of_birth": "1988",
                "bio": "A bio goes here"
            }
        }';

        $this->connection->method('sendToDB')
            ->willReturn(
                [
                    'success' => true,
                    'count' => 1,
                    'data' => [
                        0 => [
                            'id' => 203
                        ]
                    ]]
            );

        $this->assertEquals(
            ['profileID' => 203,
                'data' => [
                    0 => ['id' => 203]
                ]],
            $this->serviceKernel->execute($str)
        );
    }
}
