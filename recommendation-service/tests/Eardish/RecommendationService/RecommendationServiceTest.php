<?php
namespace Eardish\RecommendationService;
use Eardish\AppConfig;
class RecommendationServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RecommendationService
     */
    protected $service;
    protected $connection;
    protected $config;

    /**
     * @param $dto \Eardish\DataObjects\Request
     */
    protected $dto;
    public function setUp()
    {
        $this->connection = $this->getMockBuilder("Eardish\\RecommendationService\\Core\\Connection")->getMock();
        $config = new AppConfig('/eda/secret/app.json', 'local');
        $this->service = new RecommendationService($this->connection, $config);
    }

    public function testModifyProfileGenreBlend()
    {
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

        $profileId = 32;
        $genres = [1, 2, 3, 5];
        $weights = [2, 2, 2, 0];

        $expectedResult = [
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
        ];

        $this->assertEquals(
            $this->service->modifyProfileGenreBlend($profileId, $genres, $weights),
            $expectedResult
        );
    }

    public function testGetProfileGenreBlend()
    {
        $expectedResult = [
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
        ];

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

        $profileId = 32;

        $this->assertEquals(
            $this->service->getProfileGenreBlend($profileId),
            $expectedResult
        );
    }
}