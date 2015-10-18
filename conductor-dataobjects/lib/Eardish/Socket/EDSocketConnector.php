<?php
namespace Eardish\Socket;

use React\SocketClient\Connector;

class EDSocketConnector extends Connector
{
    protected $loop;
    protected $resolver;

    public function __construct($loop, $resolver)
    {
        parent::__construct($loop, $resolver);
        $this->loop = $loop;
        $this->resolver = $resolver;
    }

    public function handleConnectedSocket($socket)
    {
        return new EDSocketStream($socket, $this->loop);
    }
}