<?php
namespace Eardish\Tools;

use React\EventLoop\Factory;
use React\Dns\Resolver\Factory as DNSResolver;
use React\Socket\Connection;
use React\Stream\Stream;
use React\SocketClient\Connector;
use React\Stream\BufferedSink;

class EDSocketConnect
{
    protected $dns;
    protected $loop;
    protected $address;
    protected $port;

    public function __construct($address, $port)
    {
        if ($address == 'localhost') {
            $this->address = '127.0.0.1';
        } else {
            $this->address = $address;
        }

        $this->port = $port;
    }

    public function send($sendData)
    {
        $sock = stream_socket_client($this->address.':'.$this->port);
        stream_set_blocking($sock, 0);
//        fwrite($sock, json_encode($sendData));
//        fclose($sock);
        fwrite($sock, serialize($sendData));
//        $response = stream_get_contents($sock);
        fclose($sock);

//        return $response;
    }
}