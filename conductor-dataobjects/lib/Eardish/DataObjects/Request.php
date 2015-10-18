<?php
namespace Eardish\DataObjects;

use Eardish\DataObjects\Core\AbstractDataObject;

class Request extends AbstractDataObject implements \JsonSerializable
{
    //changes here affect json blocks in ClientData, RequestObjects in DataObjects repo, APIInterpreter and APIInterpreterTest in ephect-api repo,
    // and RequestTest

    public function isRouteable()
    {
        return $this->routeBlock->isRouteable();
    }

    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     */
    function jsonSerialize()
    {
        $result = array('action' => array(), 'data' => array(), 'meta' => array(), 'auth' => array(), 'route' => array(), 'audit' => array());

        // Set action array
        $this->buildIfSet($result['action'], array(
            'route' => $this->actionBlock->getRoute(),
            'priority' => $this->actionBlock->getPriority(),
            'response-token' => $this->actionBlock->getResponseToken()
        ));

        // Set data array
        $this->buildIfSet($result, array(
            'data' => $this->dataBlock->getDataArray()
        ));

        $this->buildIfSet($result['meta'], array(
            'current-user' => $this->metaBlock->getCurrentUser(),
            'current-profile' => $this->metaBlock->getCurrentProfile(),
            'connId' => $this->metaBlock->getConnId()
        ));

        $this->buildIfSet($result['auth'], array(
            'email' => $this->authBlock->getEmail(),
            'password' => $this->authBlock->getPassword()
        ));

        $this->buildIfSet($result['route'], array(
            'controller-method' => $this->routeBlock->getControllerMethod(),
            'controller-name' => $this->routeBlock->getControllerName(),
            'is-routeable' => $this->routeBlock->isRouteable()
        ));

        $this->buildIfSet($result, array(
            'audit' => $this->auditBlock->getLog()
        ));

        return $result;
    }
}
