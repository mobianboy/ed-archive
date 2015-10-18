<?php
//namespace Eardish\Bridge\Controllers;
//
//class MusicControllerTest extends ControllerTestCase
//{
//    /**
//     * @var MusicController
//     */
//    protected $musicController;
//
//    public function setUp()
//    {
//        parent::setUp();
//        $this->musicController = new MusicController($this->musicAgent, $this->recommendationAgent);
//    }

//    public function testAddTrack()
//    {
//        $this->dto->getRouteBlock()->setControllerMethod("addTrack");
//        $this->dto->getRouteBlock()->setControllerName("Music");
//        $this->dto->getMetaBlock()->setCurrentProfile(7);
//        $this->dto->getDataBlock()->setDataArray(array(
//            'track-info' => [
//                'track-art' => 'https://images.s3.amazonaws.com/ProfileArt/1343',
//                'track-name' => 'A Box Full of Sharp Objects',
//                'track-location' => 'https://audio.s3.amazonaws.com/MMIM/123'
//            ]
//        ));
//
//        $this->connection->method('send')->willReturn(650);
//
//        $this->assertEquals(
//            array('data' => array()),
//            $this->bridgeKernel->inbound($this->dto)
//        );
//    }

//}