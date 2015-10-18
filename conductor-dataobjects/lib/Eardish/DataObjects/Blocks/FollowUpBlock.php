<?php
namespace Eardish\DataObjects\Blocks;

class FollowUpBlock
{
    protected $type;
    protected $url;
    protected $format;

    public function __construct($type = null, $url = null, $format = null)
    {
        $this->type = $type;
        $this->url = $url;
        $this->format = $format;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return mixed
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @param mixed $format
     */
    public function setFormat($format)
    {
        $this->format = $format;
    }
}