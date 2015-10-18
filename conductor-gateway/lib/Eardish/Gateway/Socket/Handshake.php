<?php
namespace Eardish\Gateway\Socket;

class Handshake
{
    public $key;
    public $code = '258EAFA5-E914-47DA-95CA-C5AB0DC85B11';

    public function __construct($key)
    {
        $this->key = $key;
    }

    public function response()
    {
        $secAccept = base64_encode(pack('H*', sha1($this->key . $this->code)));

        $response = "HTTP/1.1 101 Switching Protocols\r\n";
        $response .= "Upgrade: websocket\r\n";
        $response .= "Connection: Upgrade\r\n";
        $response .= 'Sec-WebSocket-Accept: '.$secAccept."\r\n";
        $response .= "\r\n";

        return $response;
    }
}