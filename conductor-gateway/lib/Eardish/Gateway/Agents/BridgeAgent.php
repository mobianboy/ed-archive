<?php
namespace Eardish\Gateway\Agents;

use Eardish\Gateway\Agents\Core\AbstractAgent;

class BridgeAgent extends AbstractAgent
{
    protected $addr;
    protected $port;

    public function __construct($connection, $agentConfig)
    {
        parent::__construct($connection, $agentConfig, "bridge");
    }

    public function sendToSLC($data)
    {
        $this->send(utf8_encode(serialize($data)));
    }
}