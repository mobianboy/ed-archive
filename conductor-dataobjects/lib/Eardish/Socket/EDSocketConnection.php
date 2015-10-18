<?php
namespace Eardish\Socket;

use React\Socket\Connection as RConnection;

class EDSocketConnection extends RConnection
{
    public function __construct($stream, $loop)
    {
        parent::__construct($stream, $loop);
        $this->bufferSize = 100000;
    }
}