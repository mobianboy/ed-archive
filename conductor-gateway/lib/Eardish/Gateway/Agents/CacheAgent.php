<?php
namespace Eardish\Gateway\Agents;

use Eardish\Gateway\Agents\Core\AbstractAgent;

class CacheAgent extends AbstractAgent
{
    public function cacheable($data)
    {
        $dataArray = json_decode($data, true);
        if ($dataArray["status"]["cacheable"] == "true") {
            return true;
        } else {
            throw new \Exception("GATEWAY::unable to decode JSON", 21);
            // return false;
        }
    }

    public function addCache($data, $key, $route, $userType = null, $dataType = null)
    {
        return null;
    }

    public function expireCache($route, $key, $userType = null, $dataType = null)
    {
        return null;
    }

    public function lookupCache($route, $key, $userType = null, $dataType = null)
    {
        return null;
    }
}
