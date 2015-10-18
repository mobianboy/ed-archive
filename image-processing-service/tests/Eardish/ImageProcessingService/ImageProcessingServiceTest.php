<?php
namespace Eardish\ImageProcessingService;

use Eardish\AppConfig;
use Eardish\ImageProcessingService\Core\Config\JSONLoader;
use Eardish\ImageProcessingService\Core\Connection;

class ImageProcessingServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ImageProcessingService
     */
    protected $service;
    protected $connection;
    protected $config;

    public function setUp()
    {
        $this->connection = $this->getMockBuilder("Eardish\\ImageProcessingService\\Core\\Connection")->disableOriginalConstructor()->getMock();
        $config = new AppConfig('app.json', 'local');
        $this->service = new ImageProcessingService(new Connection(), $config);
        $jsonLoader = new JSONLoader();

        $this->config = $jsonLoader->loadJSONConfig('vendor/eardish/ephect-dataobjects/lib/Eardish/Config/DBConfig.json');
    }

    public function testPass()
    {
        $this->assertEquals('','');
    }

    /**
     * @large
     */
//    public function testPrepareProfilePic()
//    {
////        $this->connection->method('sendToDB')
////            ->willReturn(
////                [
////                    'success' => true,
////                    'count' => 1,
////
////                    'data' => [
////                        0 => [
////                            'id' => 14
////                        ]
////                    ],
////                ]
////            );
//
//        $imageSizes = [
//            "profile_art_phone_small" => 250,
//            "profile_art_phone_large" => 500,
//            "profile_art_tablet_small" => 500,
//            "profile_art_tablet_large" => 750,
//            "profile_art_thumbnail_small" => 50,
//            "profile_art_thumbnail_large" => 100
//        ];
//
//        $result = $this->service->createProfilePics(99, "profile", $imageSizes, "https://s3.amazonaws.com/uploads.hipchat.com/63785/1829081/HluNvku5An2e4EX/upload.png");
//
//        $this->assertInternalType('array', $result);
//
//        $this->assertNotCount(0, $result);
//        $this->assertEquals(
//            [
//                'conversion-success' => true,
//                'aws-success' => true,
//                'db-success' => true
//            ],
//            $result
//        );
//    }

//    public function testDelete()
//    {
//        $this->service->awsDelete("10", "service");
//    }
}
