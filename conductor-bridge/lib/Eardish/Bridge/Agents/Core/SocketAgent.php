<?php
namespace Eardish\Bridge\Agents\Core;

use React\EventLoop\LoopInterface;
use React\Socket\ConnectionException;
use React\Stream\Stream;

class SocketAgent
{
//    protected $addr;
    protected $port;

    public function __construct($agent)
    {
//        $this->addr = $agent['address'];
        $this->port = $agent['front.port'];
    }

    /**
     * @param $responseData \Eardish\DataObjects\Response
     * @return string
     * @codeCoverageIgnore
     */
    public function sendToAPI($responseData)
    {
        $fqdn = $responseData->getMetaBlock()->getFqdn();
        $socket = fsockopen($fqdn, $this->port);
        fwrite($socket, utf8_encode(serialize($responseData)));
        fclose($socket);
    }
}