<?php
//namespace WebsocketTest\Gateway;
//
//use React\EventLoop\Factory;
//use React\Dns\Resolver\Factory as DNSResolver;
//use React\SocketClient\Connector;
//
//class GatewayConnection
//{
//    protected $dns;
//    protected $loop;
//    protected $address;
//    protected $port;
//
//    public function start($address, $port, $dnsValue, $loop)
//    {
//        if ($address == 'localhost') {
//            $this->address = '127.0.0.1';
//        } else {
//            $this->address = $address;
//        }
//
//        $this->port = $port;
//        $this->loop = Factory::create();
//        //$this->loop = $loop;
//
//        $dnsResolverFactory = new DNSResolver();
//        $this->dns = $dnsResolverFactory->createCached($dnsValue, $this->loop);
//    }
//
//    public function send($sendData)
//    {
//        $connector = new Connector($this->loop, $this->dns);
//        $response = null;
//
//        $connector->create($this->address, $this->port)->then(function ($stream) use ($sendData, &$response) {
//            if (is_object($sendData)) {
//                $sendData = base64_encode(serialize($sendData));
//            } elseif (is_array($sendData)) {
//                $sendData = json_encode($sendData);
//            }
//            $stream->write($sendData);
//            $stream->on('data', function ($data) use (&$response) {
//                if (!empty($data)) {
//                    $response .= $data;
//                }
//            });
//        });
//
//        $this->loop->run();
//
//        return $response;
//    }
//}
namespace WebsocketTest\Gateway;

use React\EventLoop\Factory;
use React\Dns\Resolver\Factory as DNSResolver;
use React\SocketClient\Connector;

class GatewayConnection
{
    protected $sock;

    public function start($address, $port, $dnsValue, $loop)
    {
        if ($address == 'localhost') {
            $this->address = '127.0.0.1';
        } else {
            $this->address = $address;
        }

        $this->port = $port;
//        $this->loop = Factory::create();
        $this->loop = $loop;

        $dnsResolverFactory = new DNSResolver();
        $this->dns = $dnsResolverFactory->createCached($dnsValue, $this->loop);
    }

    public function sendBridge($sendData)
    {
        $connector = new Connector($this->loop, $this->dns);

        $response = "";
        $connector->create($this->address, $this->port)->then(function ($stream) use ($sendData, &$response) {
            if (is_object($sendData)) {
                $sendData = base64_encode(serialize($sendData));
            } elseif (is_array($sendData)) {
                $sendData = json_encode($sendData);
            }

            $stream->write($sendData);
        });

        $response = json_decode($response, true);

        return $response;
    }
}