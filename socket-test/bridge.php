<?php
require 'bootstrap.php';
require 'vendor/autoload.php';
use React\EventLoop\Factory;
use React\Socket\Server;

$loop = Factory::create();
$socket = new Server($loop);
$port = 8000;

$bridge = new \WebsocketTest\Bridge\BridgeTest();

$socket->on('connection', function($conn) use ($bridge, $loop) {
    echo 'new bridge connection' . PHP_EOL;
    $conn->on('data', function($data) use ($conn, $bridge, $loop) {
        echo $data;
        $conn->end();
        $bridge->doStuff($data, $loop);
    });
});

$socket->listen($port);
echo 'running';
$loop->run();