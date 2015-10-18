<?php
namespace Eardish\SongIngestionService;

use Monolog\Logger;
use Eardish\SongIngestionService\Core\Connection;
use Eardish\SongIngestionService\SongProcessors\WaveformGenerator;
use Eardish\SongIngestionService\SongProcessors\SongTranscoder;
use Eardish\SongIngestionService\SongProcessors\MetadataProcessor;
use Eardish\AppConfig;

class ServiceAPITest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ServiceApi
     */
    protected $serviceApi;
/*
    public function setUp()
    {
        $conn = $this->getMockBuilder('Eardish\\SongIngestionService\\Core\\Connection')
            ->setMethods(array('start'))
            ->getMock();

        $config = new AppConfig('app.json', 'local');
        $this->serviceApi = new ServiceKernel(new SongIngestionService($conn,
            new WaveformGenerator(),
            new MetadataProcessor(),
            new SongTranscoder(),
            $config),
            new Logger("ServiceLog"));
    }

    public function testPass()
    {
        $this->assertEquals(
            '',
            ''
        );
    }*/

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
