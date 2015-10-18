<?php
namespace Eardish\Bridge\Agents;

use Eardish\Bridge\Agents\Core\AbstractAgent;

class GatewayAgent extends AbstractAgent
{
    protected $sock;
    protected $port;
    protected $addr;

    public function sendToGateway($object)
    {
        $this->conn->send($object);
    }
}