<?php


class Channel
{
    /**
     * @var string
     */
    protected $name;
    /**
     * @var string
     */
    protected $url;
    /**
     * @var integer
     */
    protected $port;
    /**
     * @var string
     */
    protected $type;
    /**
     * @var boolean
     */
    protected $persist;
    /**
     * @var array
     */
    protected $channelBlock;

    /**
     * @var mixed
     */
    protected $channelHandle;

    /**
     * @param boolean
     */
    protected $lock;

    function __construct(array $channel)
    {
        $this->channelBlock = $channel;
        $this->lock = false;
    }

    function getName()
    {
        if(!(isset($this->name))) {
            $this->name = $this->channelBlock["name"];
        }
        return $this->name;
    }

    function getUrl()
    {
        if(!(isset($this->url)))) {
            $this->url = $this->channelBlock["url"];
        }
        return $this->url;
    }

    function getPort()
    {
        if(!(isset($this->port))) {
            $this->port = $this->channelBlock["type"];
        }
        return $this->port;
    }

    function getType()
    {
        if(!(isset($this->type))) {
            $this->type = $this->channelBlock["type"];
        }
        return $this->type;
    }

    function persists()
    {
        if(!(isset($this->persist))) {
            $this->persist = $this->channelBlock["persist"];
        }
        return $this->persist;
    }

    function setChannelHandle($handle)
    {
        if(!(isset($this->handle))) {
            $this->channelHandle = $handle;
            return;
        }
        throw new UnexpectedValueException("handle already set");
    }

    function getChannelHandle()
    {
        return $this->channelHandle;
    }

    /**
     * prevents multiple running tasks from overwriting each other on the channel
     * @return bool
     */
    function lock()
    {
        if(!($this->lock)) {
            $this->lock = true;
            return true;
        }
        return false;
    }

    /**
     * prevents multiple running tasks from overwriting each other on the channel
     * @return bool
     */
    function unlock()
    {
        if($this->lock) {
            $this->lock = false;
            return false;
        }
        return true;
    }
}