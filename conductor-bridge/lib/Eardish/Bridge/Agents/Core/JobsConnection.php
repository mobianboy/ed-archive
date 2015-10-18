<?php
namespace Eardish\Bridge\Agents\Core;

class JobsConnection
{
    protected $dns;
    protected $loop;
    protected $address;
    protected $port;
    protected $sock;

    public function start($address, $port)
    {
        if ($address == 'localhost') {
            $address = '127.0.0.1';
        }

        $this->address = $address;
        $this->port = $port;
    }

    public function send($sendData)
    {
        $this->sock = stream_socket_client($this->address.':'.$this->port);
        stream_set_blocking($this->sock, 0);
        fwrite($this->sock, json_encode($sendData));
        fclose($this->sock);
    }
}
