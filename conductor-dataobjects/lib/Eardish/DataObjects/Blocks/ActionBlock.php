<?php
namespace Eardish\DataObjects\Blocks;

class ActionBlock
{
    protected $route;
    protected $priority;
    protected $responseToken;

    public function __construct($route, $priority, $responseToken = null)
    {
        $this->route = $route;
        $this->priority = $priority;
        $this->responseToken = $responseToken;
    }

    /**
     * @return string
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param mixed $priority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @param mixed $route
     */
    public function setRoute($route)
    {
        $this->route = $route;
    }

    /**
     * @return null
     */
    public function getResponseToken()
    {
        return $this->responseToken;
    }

    /**
     * @param null $responseToken
     */
    public function setResponseToken($responseToken)
    {
        $this->responseToken = $responseToken;
    }
}
