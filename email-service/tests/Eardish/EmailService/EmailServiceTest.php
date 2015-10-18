<?php
namespace Eardish\EmailService;

use Eardish\AppConfig;
use Guzzle\Service\Resource\Model;

class EmailServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EmailService
     */
    protected $service;
    protected $connection;
    protected $config;


    public function setUp()
    {
        $this->connection = $this->getMockBuilder("Eardish\\EmailService\\Core\\Connection")->getMock();
        $config = new AppConfig('/eda/secret/app.json', 'local');
        $this->service = new EmailService($this->connection, $config);

    }

    public function testSendInviteCode()
    {
        $this->assertEquals(
            ['data' => ['email' => true]],
            $this->service->sendInviteCode(array('devdnr@eardish.com'), '7g09em492m', 'Eardish User')
        );
    }

    public function testSendResetPassCode()
    {
        $this->assertEquals(
            ['data' => ['email' => true]],
            $this->service->sendResetPassCode(array('devdnr@eardish.com'), '0asdf87851a', 'Eardish User2')
        );

    }
}
