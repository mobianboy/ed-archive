<?php
namespace Eardish\DataObjects;

use Eardish\DataObjects\Blocks\AuditBlock;
use Eardish\DataObjects\Blocks\DataBlock;
use Eardish\DataObjects\Blocks\FollowUpBlock;
use Eardish\DataObjects\Blocks\MessageBlock;
use Eardish\DataObjects\Blocks\MetaBlock;
use Eardish\DataObjects\Blocks\StatusBlock;

class ResponseTest extends \PHPUnit_Framework_TestCase
{
    protected $connId;
    protected $data;

    /**
     * @var Response
     */
    protected $response;

    public function setUp()
    {

        $metaBlock = new MetaBlock();
        $metaBlock->setApiVersion(11.7);
        $metaBlock->setModelType('user');
        $metaBlock->setConnId('45');
        $metaBlock->setListType('trackList');
        $this->response = new Response( array(
            new DataBlock(
                array("email" => "test@eardish.com", "password" => "secretPassword")),
            new AuditBlock(),
            new StatusBlock('21', 'Successful'),
            new FollowUpBlock('api', 'facebook.com'),
            new MessageBlock(array())));
        $this->response->injectBlock($metaBlock);
    }

    public function testJsonEncode()
    {
        $this->assertEquals(
            '{"data":{"email":"test@eardish.com","password":"secretPassword"},"meta":{"connId":"45","apiVersion":11.7,"modelType":"user","listType":"trackList"},"followUp":{"type":"api","url":"facebook.com"},"message":{"message":[]},"status":{"code":"21","message":"Successful"}}',
            json_encode($this->response)
        );
    }

    public function testGetData()
    {
        $this->assertInstanceOf(
            "Eardish\\DataObjects\\Blocks\\DataBlock",
            $this->response->getDataBlock()
        );
    }

    public function testGetAudit()
    {
        $this->assertInstanceOf(
            "Eardish\\DataObjects\\Blocks\\AuditBlock",
            $this->response->getAuditBlock()
        );
    }

    public function testGetStatus()
    {
        $this->assertInstanceOf(
            "Eardish\\DataObjects\\Blocks\\StatusBlock",
            $this->response->getStatusBlock()
        );
    }

    public function testGetFollowUp()
    {
        $this->assertInstanceOf(
            "Eardish\\DataObjects\\Blocks\\FollowUpBlock",
            $this->response->getFollowUpBlock()
        );
    }

    public function testGetMessage()
    {
        $this->assertInstanceOf(
            "Eardish\\DataObjects\\Blocks\\MessageBlock",
            $this->response->getMessageBlock()
        );
    }

    public function testInjectBlock()
    {
        $this->response->injectBlock(new StatusBlock('21', 'success'));
        $this->assertEquals(
            new StatusBlock('21', 'success'),
            $this->response->getStatusBlock()
        );
    }

    public function testBlockExists()
    {
        $response = new Response(array());
        $response->injectBlock(new StatusBlock('21', 'success'));

        $this->assertFalse(
            $response->blockExists('data')
        );

        $this->assertTrue(
            $response->blockExists('status')
        );
    }
}
