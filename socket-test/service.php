<?php
require 'bootstrap.php';
require 'vendor/autoload.php';
use React\EventLoop\Factory;
use React\Socket\Server;

$loop = Factory::create();
$socket = new Server($loop);
$port = 8001;

$service = new \WebsocketTest\Service\ServiceTest($loop);

$socket->on('connection', function($conn) use ($service, $loop) {
    $conn->on('data', function($data) use ($service, $conn, $loop) {
        echo $data;
        $result = $service->doStuff($data, $conn);
        echo 'writing back data';
        $conn->end();
    });
});

$socket->listen($port);
echo 'running';
$loop->run();