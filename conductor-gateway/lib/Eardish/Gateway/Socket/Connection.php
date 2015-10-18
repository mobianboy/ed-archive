<?php
namespace Eardish\Gateway\Socket;

use React\Socket\Connection as RConnection;

class Connection extends RConnection
{
    protected $resourceId;

    /**
     * Used to determine whether or not the connection has successfully completed the websocket handshake
     *
     * @var bool
     */
    protected $upgraded = false;

    /**
     * Determines whether or not the connection is authed
     *
     * @var bool
     */
    protected $auth = false;
    protected $route = false;

    /**
     * Used to store the current user profileId.
     *
     * @var int
     */
    protected $profileId = 0;
    protected $framer = null;

    protected $connectTime;

    /**
     * when the buffer is overread in the framer, there is no way to push
     * the data back.  so the overread data has to be stored in the connection
     * until the next time data is received, and then it starts off the stream
     * @var
     */
    protected $overreadBuffer;

    /**
     * @param $socket
     * @param \React\EventLoop\LoopInterface $loop
     * @param $id
     * @codeCoverageIgnore
     */
    public function __construct($socket, $loop, $id)
    {
        $this->setResourceId($id);
        parent::__construct($socket, $loop);
        $this->bufferSize = 200000;
    }

    public function isUpgraded()
    {
        return $this->upgraded;
    }

    public function upgrade()
    {
        $this->upgraded = true;
    }

    public function setResourceId($id)
    {
        $this->resourceId = $id;
    }

    public function getResourceId()
    {
        return $this->resourceId;
    }

    public function isConnAuthed()
    {
        return $this->auth;
    }

    public function setConnAuth($authed = true)
    {
        $this->auth = $authed;
    }

    public function setProfileId($profileId)
    {
        $this->profileId = $profileId;
    }

    public function getProfileId()
    {
        return $this->profileId;
    }

    public function isProfileSet()
    {
        return isset($this->profileId);
    }

    public function isConnRouted()
    {
        return $this->route;
    }

    public function setConnRoute($routed = true)
    {
        $this->route = $routed;
    }

    public function setFramer($framer)
    {
        $this->framer = $framer;
    }

    public function getFramer()
    {
        if(isset($this->framer)) {
            return $this->framer;
        }
        return null;
    }

    public function releaseFramer()
    {
        unset($this->framer);
    }

    public function setConnectTime($time)
    {
        $this->connectTime = $time;
    }

    public function getConnectTime()
    {
        return $this->connectTime;
    }

    /**
     * get the buffer overrun from the last data read.
     * @return mixed
     */
    public function getOverreadBuffer()
    {
        return $this->overreadBuffer;
    }

    public function getOverreadBufferSize()
    {
        return strlen($this->overreadBuffer);
    }
}
