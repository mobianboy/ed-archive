<?php
namespace Eardish\Gateway;

/**
 * User-Connection-Route Matrix
 *
 * Manages the canonical storage for React connections
 * holds all active connections by
 *     connectionId ==> react connection reference
 *     userId       ==> connectionId
 *     route        ==> connectionId
 *
 * also adds helper storage
 *     connectionId ==> route
 *     connectionId ==> userId
 *
 * component used by Gateway entry point and Sync Manager
 *
 */
class ConnectionMapper
{
    /**
     * @var Socket\Connection[]
     */
    protected $connMap = array();
    protected $userMap = array(); // userId to connId mapping  (1 to many)
    protected $routeMap = array(); // route to connId mapping (1 to many)
    protected $connRouteMap = array();
    protected $connUserMap = array();
    protected $tokenMap = array();
    protected $monitor = array();
    //protected $connCreate; // connectionId creation time since epoch
    //protected $connActive; // connectionId last modified time since epoch
    /**
     * add the connection - create and return a connectionId
     *
     * @param &$connObj Socket\Connection
     * @return string
     *
     * the connectionId is a hash of the serialized React connection object
     */
    public function connect($connObj)
    {
        $connId = $connObj->getResourceId();

        $connObj->setConnectTime(time());
        // Add conn to maps
        $this->connMap[$connId] = $connObj;
        // return the generated connectionId
        return $connId;
    }
    /**
     * delete the connection and all references to it
     *
     * @param $connId integer
     * @return void
     */
    public function disconnect($connId)
    {
        // If the connection is upgraded (it handshaked correctly), then unset all of its UCR references.
        // Otherwise, only remove it from the connMap (the only place it was set)
        if ($this->connMap[$connId]->isUpgraded()) {
            $this->disconnectUser($connId);
            $this->disconnectRoute($connId);
        }

        if ($this->isConnMonitor($connId)) {
            unset($this->monitor[$connId]);
        }
        unset($this->connMap[$connId]);
    }

    public function disconnectUser($connId)
    {
        if ($this->connMap[$connId]->isConnAuthed()) {
            $userId = $this->connUserMap[$connId];
            $userArray =& $this->userMap[$userId];
            unset($userArray[$connId], $this->connUserMap[$connId]);
            $this->connMap[$connId]->setConnAuth(false);
        }
    }

    public function disconnectRoute($connId)
    {
        if ($this->connMap[$connId]->isConnRouted()) {
            $route = $this->connRouteMap[$connId];
            $routeArray =& $this->getRouteArray($route);
            unset($routeArray['__conns'][$connId], $this->connRouteMap[$connId]);
            $this->connMap[$connId]->setConnRoute(false);
        }
    }

    /**
     * update the route on a connection
     *
     * @param $connId string
     * @param $newRoute string
     */
    public function updateRoute($connId, $newRoute)
    {
        $exists = $this->getRouteByConn($connId);
        if ($exists) {
            $old =& $this->findInRoute($connId);
            $new =& $this->getRouteArray($newRoute, true);
            $new['__conns'][$connId] = $old['__conns'][$connId];
            $this->connRouteMap[$connId] = $newRoute;
            unset($old[$connId]);
        } else {
            $this->setRoute($connId, $newRoute);
        }
    }
    public function setRoute($connId, $route)
    {
        $routePath =& $this->getRouteArray($route, true);
        $routePath['__conns'][$connId] = $this->connMap[$connId];
        $this->connRouteMap[$connId] = $route;
    }
    public function setUser($connId, $userId)
    {
        $this->userMap[$userId][$connId] = $this->connMap[$connId];
        $this->connUserMap[$connId] = $userId;
    }
    public function &findInRoute($connId)
    {
        $route = $this->connRouteMap[$connId];
        return $this->getRouteArray($route);
    }
    public function getByUser($userId)
    {
        return $this->getConnObjs(array_keys($this->userMap[$userId]));
    }
    public function getByRoute($route)
    {
        $routePath =& $this->getRouteArray($route);
        return $this->getConnObjs(array_keys($routePath['__conns']));
    }
    /**
     * given a connectionId, return the React connection object instance
     * if it doesn't exist, return null
     * @param $connId string
     * @return Socket\Connection|null
     */
    public function getConnObj($connId)
    {
        if (isset($this->connMap[$connId])) {
            return $this->connMap[$connId];
        }
        return null;
    }
    public function getConnObjs($arr)
    {
        $result = array();
        foreach ($arr as $val) {
            $result[] = $this->getConnObj($val);
        }
        return $result;
    }
    public function getUserByConn($connId)
    {
        return (isset($this->connUserMap[$connId])) ? $this->connUserMap[$connId] : false;
    }
    public function getRouteByConn($connId)
    {
        return (isset($this->connRouteMap[$connId])) ? $this->connRouteMap[$connId] : false;
    }
    /**
     * @return array
     */
    public function getConnMap()
    {
        $conns = array();

        foreach ($this->connMap as $connID => $conn) {
            $conns[$connID] = [
                'remote' => $conn->getRemoteAddress(),
                'profile' => $conn->getProfileId(),
                'connected-for' => (time() - $conn->getConnectTime())
            ];
        }

        return $conns;
    }

    public function &getRouteArray($route, $make = false)
    {
        $route = trim(rtrim($route, "/"), "/");
        $parts = explode("/", $route);
        $routeMap =& $this->routeMap;
        foreach ($parts as $part) {
            if (!isset($routeMap[$part]) && $make) {
                $routeMap[$part] = array();
                $routeMap[$part]['__conns'] = array();
            }
            $routeMap =& $routeMap[$part];
        }
        return $routeMap;
    }

    public function setConnMonitor($connId)
    {
        $this->monitor[$connId] = $this->connMap[$connId];
    }

    public function setConnResponseTokenData($connId, $responseToken, $data)
    {
        $this->tokenMap[$connId][$responseToken] = $data;
    }

    public function getConnResponseTokenData($connId, $responseToken)
    {
        if (isset($this->tokenMap[$connId][$responseToken])) {
            $data = $this->tokenMap[$connId][$responseToken];
            unset($this->tokenMap[$connId][$responseToken]);

            return $data;
        } else {
            return false;
        }
    }

    public function isConnMonitor($connid)
    {
        return isset($this->monitor[$connid]);
    }

    /**
     * @return Socket\Connection[]
     */
    public function getConnMonitors()
    {
        return $this->monitor;
    }
}