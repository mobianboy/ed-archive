<?php
namespace Eardish\SongIngestionService\Core;

use \Eardish\Exceptions\EDConnectionException;
use \Eardish\Exceptions\EDConnectionWriteException;
use \Eardish\Exceptions\EDConnectionReadException;
use \Eardish\Exceptions\EDException;
use \Eardish\Exceptions\EDTransportException;
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

    public function sendToDB($sendData)
    {
        $connector = new Connector($this->loop, $this->dns);

        if (!$this->address || !$this->port) {
            throw new EDConnectionException(
                "PROFILE: unable to connect to Notation DB service at $this->address port: $this->port"
            );
        }

        $response = "";
        $connector->create($this->address, $this->port)->then(function ($stream) use ($sendData, &$response) {
            if (is_object($sendData)) {
                $sendData = base64_encode(serialize($sendData));
            } elseif (is_array($sendData)) {
                $sendData = json_encode($sendData);
            }
            $stream->write($sendData);
            $stream->on('data', function ($data) use (&$response) {
                if (!empty($data)) {
                    $response .= $data;
                }
            });
        });

        $this->loop->run();

        $response = json_decode($response, true);
        if (array_key_exists('exception', $response)) {
            throw new EDException($response['exception']['message'], $response['exception']['code']);
        }
        return $response;
    }
}