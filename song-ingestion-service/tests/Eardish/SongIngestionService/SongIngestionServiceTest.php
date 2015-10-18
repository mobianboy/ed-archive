<?php
namespace Eardish\SongIngestionService;

use Eardish\SongIngestionService\Core\Connection;
use Eardish\AppConfig;
use Eardish\SongIngestionService\Core\Config\JSONLoader;

class SongIngestionServiceTest extends \PHPUnit_Framework_TestCase
{
    /*
     * @var SongIngestionService
     */
    protected $service;
    protected $connection;
    protected $config;

    public function setUp()
    {
        //  $this->connection = $this->getMockBuilder("Eardish\\ImageProcessingService\\Core\\Connection")->disableOriginalConstructor()->getMock();
        $config = new AppConfig('/eda/secret/app.json', 'local');
        $this->service = new SongIngestionService(new Connection(), $config);
        $jsonLoader = new JSONLoader();

        $this->config = $jsonLoader->loadJSONConfig('vendor/eardish/ephect-dataobjects/lib/Eardish/Config/DBConfig.json');
    }


    public function testEquals()
    {
        $this->assertEquals('','');
    }

    /**
     * @large
     */
    /*    public function testStageTrack()
        {
            $filename = $this->service->transcodeStageTrack(2);
            $this->assertEquals($filename, "0432342.mp3");


        }
    8?
        /**
         * @large
         */
   /* public function testAll()
    {
        // $resp = $this->service->transcodeAudioToAllFormats(2,"0","-1");

    }
*/
    public function testPushAndClean()
    {
        $resp = $this->service->transcodePushAndCleanFile(301,"2.LOW.MP3","/home/kryptyk/testbed/outputs");
    }

}
