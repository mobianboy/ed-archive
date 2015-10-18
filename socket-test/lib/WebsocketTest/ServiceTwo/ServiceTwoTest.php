<?php
namespace WebsocketTest\ServiceTwo;

use WebsocketTest\ServiceTwo\ServiceTwoConnection;

class ServiceTwoTest
{
    protected $loop;

    public function __construct($loop)
    {
        $this->loop = $loop;
    }

    public function doStuff($data)
    {
        $data = $data;
        echo $data;
        $data = intval($data);
        $numbers = [];
        $count = 0;
        for ($i = 0; $i < $data; $i++) {
            $count = $count+1;
            $numbers[$i] = rand(1, $data) * 10 / 25;
        }
        $conn->write('hello');
        return $numbers;
//        $connection = new ServiceConnection();
//        $connection->start('localhost', 8001);
//        $connection->sendBridge($data);
    }
}