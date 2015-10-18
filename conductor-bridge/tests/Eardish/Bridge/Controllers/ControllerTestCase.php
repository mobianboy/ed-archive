<?php
namespace Eardish\Bridge\Controllers;

use Eardish\AppConfig;
use Eardish\Bridge\BridgeKernel;
use Eardish\Bridge\Config\JSONLoader;
use Eardish\DataObjects\Request;
use Eardish\DataObjects\Blocks\ActionBlock;
use Eardish\DataObjects\Blocks\AuthBlock;
use Eardish\DataObjects\Blocks\DataBlock;
use Eardish\DataObjects\Blocks\MetaBlock;
use Eardish\DataObjects\Blocks\RouteBlock;
use Eardish\DataObjects\Blocks\AuditBlock;
use Eardish\Bridge\Agents\EmailAgent;
use Eardish\Bridge\Agents\GroupAgent;
use Eardish\Bridge\Agents\MusicAgent;
use Eardish\Bridge\Agents\PhotoAgent;
use Eardish\Bridge\Agents\PlaylistAgent;
use Eardish\Bridge\Agents\RecommendationAgent;
use Eardish\Bridge\Agents\SocialAgent;
use Eardish\Bridge\Agents\SongIngestionAgent;
use Eardish\Bridge\Agents\UserAgent;
use Eardish\Bridge\Agents\ImageProcessingAgent;
use Eardish\Bridge\Agents\AuthAgent;
use Eardish\Bridge\Agents\ProfileAgent;
use Eardish\Bridge\Agents\Core\Connection;

class ControllerTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Request
     */
    protected $dto;

    /**
     * @var GroupAgent
     */
    protected $groupAgent;

    /**
     * @var MusicAgent
     */
    protected $musicAgent;

    /**
     * @var PhotoAgent
     */
    protected $photoAgent;

    /**
     * @var PlaylistAgent
     */
    protected $playlistAgent;

    /**
     * @var SocialAgent
     */
    protected $socialAgent;

    /**
     * @var SongIngestionAgent
     */
    protected $songIngestionAgent;

    /**
     * @var UserAgent
     */
    protected $userAgent;

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var EmailAgent
     */
    protected $emailAgent;

    /**
     * @var RecommendationAgent
     */
    protected $recommendationAgent;

    /**
     * @var ImageProcessingAgent
     */
    protected $imageProcessingAgent;

    /**
     * @var AuthAgent
     */
    protected $authAgent;

    /**
     * @var ProfileAgent
     */
    protected $profileAgent;

    /**
     * @var BridgeKernel
     */
    protected $bridgeKernel;

    public function setUp()
    {
        $this->dto =new Request(array(new ActionBlock("/group/12345", "10"), new DataBlock(array("bio" => "i am  a bio")),
            new MetaBlock(), new AuthBlock("test@eardish.com", "password"), new RouteBlock(), new AuditBlock()));

        $this->connection = $this->getMockBuilder("Eardish\\Bridge\\Agents\\Core\\Connection")
            ->disableOriginalConstructor()
            ->getMock();

        $jsonLoader = new JSONLoader();
        $agents = $jsonLoader->loadJSONConfig('app.json');

        $agents = $agents['default']['agents'];

        $priority = "10";

//
//        $this->socialAgent = new SocialAgent($this->connection, $priority, $agents['social']);
//        $this->musicAgent = new MusicAgent($this->connection, $priority, $agents['music']);
//        $this->photoAgent = new PhotoAgent($this->connection, $priority, $agents['photo']);
//        $this->playlistAgent = new PlaylistAgent($this->connection, $priority, $agents['playlist']);
//        $this->userAgent = new UserAgent($this->connection, $priority, $agents['user']);
//        $this->songIngestionAgent = new SongIngestionAgent($this->connection, $priority, $agents['songingestion']);
//        $this->groupAgent = new GroupAgent($this->connection, $priority, $agents['group']);
//        $this->emailAgent = new EmailAgent($this->connection, $priority, $agents['email']);
//        $this->recommendationAgent = new RecommendationAgent($this->connection, $priority, $agents['recommendation']);
//        $this->imageProcessingAgent = new ImageProcessingAgent($this->connection, $priority, $agents['imageprocessing']);
//        $this->authAgent = new AuthAgent($this->connection, $priority, $agents['auth']);
//        $this->profileAgent = new ProfileAgent($this->connection, $priority, $agents['profile']);
    }

    public function newBridgeKernel()
    {
        return new BridgeKernel($this->connection, new AppConfig('app.json', 'default'));
    }
}