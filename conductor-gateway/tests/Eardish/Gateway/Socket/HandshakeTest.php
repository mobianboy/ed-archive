<?php
namespace Eardish\Gateway\Socket;

class HandshakeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Handshake
     */
    protected $handshake;

    public function setUp()
    {
        $headers = "GET / HTTP/1.1\n
            Host: 127.0.0.1:8080\n
            Connection: Upgrade\n
            Pragma: no-cache\n
            Cache-Control: no-cache\n
            Upgrade: websocket\n
            Origin: null\n
            Sec-WebSocket-Version: 13\n
            User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.115 Safari/537.36\n
            Accept-Encoding: gzip, deflate, sdch\n
            Accept-Language: en-US,en;q=0.8\n
            Sec-WebSocket-Key: U8tJKlv6Cxu4ddLigmMFIA==\n
            Sec-WebSocket-Extensions: permessage-deflate; client_max_window_bits\n";

        $verifier = new Verifier($headers);
        $this->handshake = new Handshake($verifier->getWSKey());
    }

    public function testResponse()
    {
        $handshakeResponse = "HTTP/1.1 101 Switching Protocols\r\nUpgrade: websocket\r\nConnection: Upgrade\r\nSec-WebSocket-Accept: jqkmS/yvQHliG+vjDKBa34kfWCQ=\r\n\r\n";

        $this->assertEquals(
            $handshakeResponse,
            $this->handshake->response()
        );
    }
}