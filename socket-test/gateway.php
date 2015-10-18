<?php
require 'bootstrap.php';
require 'vendor/autoload.php';

use React\EventLoop\Factory;
use React\Socket\Server;

$loop = Factory::create();
$web = new Server($loop);
$service = new Server($loop);
$gateway = new \WebsocketTest\Gateway\GatewayTest($loop);
$webPort = 7000;
$servicePort = 7001;

$web->on('connection', function($conn) use ($gateway) {
    $conn->on('data', function($data) use ($conn, $gateway) {
        $gateway->doStuff($data);
    });
});

$web->listen($webPort);
$web->listen($servicePort);
echo 'running';
$loop->run();