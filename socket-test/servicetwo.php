<?php
require 'bootstrap.php';
require 'vendor/autoload.php';
use React\EventLoop\Factory;
use React\Socket\Server;

$loop = Factory::create();
$socket = new Server($loop);
$port = 8002;

$socket->on('connection', function($conn) use ($loop) {
    $conn->on('data', function($data) use ($conn, $loop) {
        $service = new \WebsocketTest\Service\ServiceTest($loop);
        echo $data;
        $result = $service->doStuff($data, $conn);
        echo 'writing back data';
        $conn->end();
    });
});

$socket->listen($port);
echo 'running';
$loop->run();