<?php
namespace Eardish\DataObjects\Blocks;

/**
 * Class RouteBlock
 * @package Eardish\EphectAPI\DataObjects\RequestObjects
 *
 * part of the Request object that passes from Ephect API to SLC
 *
 * RouteBlock is filled in by AuraRouter after a match is found
 * for the route.
 *
 * Attributes for this class will grow, as well as its accessors
 *
 */
class RouteBlock
{
    protected $controllerName;
    protected $controllerMethod;

    public function __construct($controllerName = null, $controllerMethod = null)
    {
        $this->controllerName = $controllerName;
        $this->controllerMethod = $controllerMethod;
    }

    /**
     * @return null
     */
    public function getControllerName()
    {
        return $this->controllerName;
    }

    /**
     * @param null $controllerName
     */
    public function setControllerName($controllerName)
    {
        $this->controllerName = $controllerName;
    }

    /**
     * @return null
     */
    public function getControllerMethod()
    {
        return $this->controllerMethod;
    }

    /**
     * @param null $controllerMethod
     */
    public function setControllerMethod($controllerMethod)
    {
        $this->controllerMethod = $controllerMethod;
    }

    /**
     * @return boolean
     */
    public function isRouteable()
    {
        if (isset($this->controllerMethod) && isset($this->controllerName)) {
            return true;
        } else {
            return false;
        }
    }
}