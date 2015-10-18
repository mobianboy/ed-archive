<?php
namespace WebsocketTest\Bridge;


class BridgeConnection
{
    protected $sock;
    protected $addr;
    protected $port;

    /**
     * @param $addr String
     * @param $port String
     */
    public function start($addr, $port, $dnsValue, $loop)
    {
        $this->addr = $addr;
        $this->port = $port;

    }

    public function sendService($request)
    {
        // todo -- stream_socket_client sends notices on failures -- work there
        $this->sock = stream_socket_client($this->addr.':'.$this->port);
        //stream_set_blocking($this->sock, 0);
        $status = fwrite($this->sock, $request);

        $response = stream_get_contents($this->sock);

        fclose($this->sock);

        $result = $response;


        return $result;
    }
}