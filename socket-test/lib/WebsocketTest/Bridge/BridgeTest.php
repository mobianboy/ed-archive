<?php
namespace WebsocketTest\Bridge;

use WebsocketTest\Bridge\BridgeConnection;

class BridgeTest
{
    public function __construct()
    {

    }

    public function doStuff($data, $loop)
    {
        $connection = new BridgeConnection();
        if ($data > 100000) {
            $connection->start('localhost', 8001, "8.8.8.8", $loop);
        } else {
            $connection->start('localhost', 8002, "8.8.8.8", $loop);
        }
        $response = $connection->sendService($data);

        var_dump($response);
    }
}