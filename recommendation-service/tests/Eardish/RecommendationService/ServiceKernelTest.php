<?php
namespace Eardish\RecommendationService;
use Eardish\AppConfig;
use Monolog\Logger;
use Eardish\RecommendationService\Core\Connection;
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
        $this->connection = $this->getMockBuilder("Eardish\\RecommendationService\\Core\\Connection")->getMock();
        $logger = new Logger("service");
        $config = new AppConfig('/eda/secret/app.json', 'local');
        $this->serviceKernel = new ServiceKernel(new RecommendationService($this->connection, $config), $logger);
        // Mock the serviceAPI
    }
    public function testExecute()
    {
        // pass some data into the serviceKernel, get back the result
        $arr = array(
            "method" => "modifyProfileGenreBlend",
            "priority" => "10",
            "params" => array(
                "profileId" => 10,
                "genreIds" => array(
                    1, 2, 3, 5
                ),
                "weights" => array(
                    2, 2, 2, 0
                )
            )
        );
        $str = json_encode($arr);

        $dbResponse = [
            'success' => true,
            'data' => [
                0 => [
                    "profile_id" => 32,
                    "genre_id" => 1,
                    "weight" => 2
                ],
                1 => [
                    "profile_id" => 32,
                    "genre_id" => 2,
                    "weight" => 2
                ],
                2 => [
                    "profile_id" => 32,
                    "genre_id" => 3,
                    "weight" => 2
                ],
                3 => [
                    "profile_id" => 32,
                    "genre_id" => 5,
                    "weight" => 0
                ]
            ]];

        $this->connection->method('sendToDB')
            ->willReturn(
                $dbResponse
        );

        $this->assertEquals(
            [
                'anti' => [
                    0 => [
                        "genreId" => 5,
                        "weight" => 0
                    ]
                ],
                "preferred" => [
                    0 => [
                        "genreId" => 1,
                        "weight" => 2
                    ],
                    1 => [
                        "genreId" => 2,
                        "weight" => 2
                    ],
                    2 => [
                        "genreId" => 3,
                        "weight" => 2
                    ]
                ]
            ],
            $this->serviceKernel->execute($str)
        );
    }
}
