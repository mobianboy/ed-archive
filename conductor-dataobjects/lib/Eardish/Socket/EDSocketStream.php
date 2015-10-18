<?php
namespace Eardish\Socket;

use React\Stream\Stream;

class EDSocketStream extends Stream
{
    public function __construct($stream, $loop)
    {
        parent::__construct($stream, $loop);
        $this->bufferSize = 100000;
    }
}