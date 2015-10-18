<?php
namespace Eardish\ImageProcessingService;
//
use Eardish\ImageProcessingService\Core\Config\JSONLoader;
use Eardish\ImageProcessingService\Core\Connection;
use Monolog\Logger;
use Eardish\AppConfig;

class ServiceKernelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ServiceKernel
     */
    protected $serviceApi;
    protected $config;

    public function setUp()
    {
        //$connection = $this->getMockBuilder("Eardish\\ImageProcessingService\\Core\\Connection")->disableOriginalConstructor()->getMock();
        $logger = new Logger("service");
        $config = new AppConfig('app.json', 'local');
        $this->serviceApi = new ServiceKernel(new ImageProcessingService(new Connection(), $config), $logger);
        // Mock the serviceAPI
    }

    public function testPass()
    {
        $this->assertTrue(true);
    }

    /**
     * @large
     */
    public function testExecute()
    {
        $data['method'] = "addArt";
        $data['priority'] = 10;
        $data['params']['profileId'] = 10;
        $data['params']['title'] = "image01";
        $data['params']['url'] = "default";
        $data['params']['description'] = "no description";
        $data['params']['type'] = "profile";
        $data['params']['sizes'] = [
            "profile_art_phone_small" => 250,
            "profile_art_phone_large" => 500,
            "profile_art_tablet_small" => 500,
            "profile_art_tablet_large" => 750,
            "profile_art_thumbnail_small" => 50,
            "profile_art_thumbnail_large" => 100
        ];

        $result = $this->serviceApi->execute(json_encode($data));
        $this->assertEquals(true, $result['success']);
    }

//    public function testSend()
//    {
//        // pass some data into the serviceAPI, get back the result
//        $str = 'here is some data';
//
//        $this->assertEquals(
//            'here is some data',
//            $this->serviceApi->execute($str)
//        );
//    }
}
