<?php
namespace Eardish\AnalyticsService\Core;

class Connection
{
    protected $dns;
    protected $loop;
    protected $address;
    protected $port;
    protected $sock;

    public function start($address, $port)
    {
        if ($address == 'localhost') {
            $this->address = '127.0.0.1';
        } else {
            $this->address = $address;
        }

        $this->port = $port;
    }

    public function sendToDB($sendData)
    {
        $this->sock = stream_socket_client($this->address.':'.$this->port);
        stream_set_blocking($this->sock, 0);
        fwrite($this->sock, json_encode($sendData));
        fclose($this->sock);
    }
}