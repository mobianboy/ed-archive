<?php
namespace WebsocketTest;

use React\EventLoop\Factory;
use React\Dns\Resolver\Factory as DNSResolver;
use React\SocketClient\Connector;

class Connection
{
    protected $dns;
    protected $loop;
    protected $address;
    protected $port;

    public function start($address, $port, $dnsValue)
    {
        if ($address == 'localhost') {
            $this->address = '127.0.0.1';
        } else {
            $this->address = $address;
        }

        $this->port = $port;
        $this->loop = Factory::create();

        $dnsResolverFactory = new DNSResolver();
        $this->dns = $dnsResolverFactory->createCached($dnsValue, $this->loop);
    }

    public function send($sendData)
    {
        $connector = new Connector($this->loop, $this->dns);
        $response = null;

        $connector->create($this->address, $this->port)->then(function ($stream) use ($sendData, &$response) {
            if (is_object($sendData)) {
                $sendData = base64_encode(serialize($sendData));
            } elseif (is_array($sendData)) {
                $sendData = json_encode($sendData);
            }
            $stream->write($sendData);
            $stream->on('data', function($data) use (&$response){
                if (!$response) {
                    $response = json_decode($data, true);
                }
            });
        });

        $this->loop->run();

        return $response;
    }
}
