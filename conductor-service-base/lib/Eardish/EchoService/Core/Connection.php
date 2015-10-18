<?php
namespace Eardish\EchoService\Core;

class Connection
{
    protected $sock;
    protected $addr;
    protected $port;

    /**
     * @param $addr String
     * @param $port String
     */
    public function start($addr, $port)
    {
        $this->addr = $addr;
        $this->port = $port;
    }

    /**
     * @param request
     * @return string
     * @codeCoverageIgnore
     */
    public function sendToDB($request)
    {
        $this->sock = stream_socket_client($this->addr.':'.$this->port);

        fwrite($this->sock, json_encode($request));

        $response = stream_get_contents($this->sock);

        fclose($this->sock);

        return json_decode($response, true);
    }
}
