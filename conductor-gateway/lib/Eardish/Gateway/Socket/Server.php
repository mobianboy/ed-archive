<?php

namespace Eardish\Gateway\Socket;

use React\EventLoop\LoopInterface;
use React\Socket\Server as RServer;

class Server extends RServer
{
    private $connId = 0;
    private $cloop;

    public function __construct(LoopInterface $loop)
    {
        $this->cloop = $loop;

        parent::__construct($loop);
    }

    public function createConnection($socket)
    {
        return new Connection($socket, $this->cloop, ++$this->connId);
    }
}