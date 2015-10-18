<?php
namespace WebsocketTest\Gateway;

use WebsocketTest\Gateway\GatewayConnection;

class GatewayTest
{
    protected $loop;

    public function __construct($loop)
    {
        $this->loop = $loop;
    }

    public function doStuff($data)
    {
//        $data = intval($data);
//        $numbers = [];
//        for ($i = 0; $i < $data; $i++) {
//            $numbers[$i] = rand(1, $data);
//        }
//        print_r($numbers);
        $connection = new GatewayConnection();
        $connection->start('localhost', 8000, '8.8.8.8', $this->loop);
        $connection->sendBridge($data);
    }
}