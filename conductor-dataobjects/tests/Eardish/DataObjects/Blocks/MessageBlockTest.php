<?php
namespace Eardish\DataObjects\Blocks;

class MessageBlockTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MessageBlock
     */
    protected $object;

    public function setUp()
    {
        $this->object = new MessageBlock();
    }

    public function testGetSetMessages()
    {
        $this->object->addMessage('server', 'toast', 'user has been logged in successfully');
        $this->assertEquals(
            array(0 => array('source' => 'server', 'type' => 'toast', 'content' => 'user has been logged in successfully')),
            $this->object->getMessages()
        );

        $this->object->addMessage('server', 'toast', 'user has been logged out successfully');
        $this->assertEquals(
            array(
                0 => array('source' => 'server', 'type' => 'toast', 'content' => 'user has been logged in successfully'),
                1=>array('source' => 'server', 'type' => 'toast', 'content' => 'user has been logged out successfully')),
            $this->object->getMessages()
        );
    }
}