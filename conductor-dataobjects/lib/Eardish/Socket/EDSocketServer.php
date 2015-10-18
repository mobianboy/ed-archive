<?php
namespace Eardish\Socket;

use React\EventLoop\LoopInterface;
use React\Socket\Server as RServer;

class EDSocketServer extends RServer
{
    private $cloop;

    public function __construct(LoopInterface $loop)
    {
        $this->cloop = $loop;
        parent::__construct($loop);
    }

    public function createConnection($socket)
    {
        return new EDSocketConnection($socket, $this->cloop);
    }
}