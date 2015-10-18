<?php

require('bootstrap.php');
require('vendor/autoload.php');

// TODO update these use statements to the correct namespace and service
use \Eardish\EchoService\ServiceKernel;
use \Eardish\EchoService\EchoService;
use \Eardish\EchoService\Core\Connection;

use React\Socket\Server;
use React\EventLoop\Factory;

// MONOLOG
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\MemoryUsageProcessor;
use Monolog\Processor\GitProcessor;

$cli = new League\CLImate\CLImate();

$cli->arguments->add([
    'env' => [
        'prefix' => 'e',
        'longPrefix' => 'env',
        'description' => 'Select the environment that this server will start in.',
        'defaultValue' => 'local'
    ],
    'help' => [
        'longPrefix'    => 'help',
        'description'   => 'Display this help document',
        'noValue'       => true
    ]
]);

try {
    $cli->arguments->parse();
} catch (\Exception $e) {
    $cli->red()->out('One of the required parameters was missing. Please use the --help option for more detail.');
    exit();
}


if ($cli->arguments->defined('help')) {
    $cli->usage();
    exit();
}

$configFile = realpath(__DIR__). '/app.json';

$config = new \Eardish\AppConfig($configFile, $cli->arguments->get('env'));
// default port
$port   = $config->get('port');
$name   = $config->get('name');
if ($config->get('self-fqdn')) {
    $host = $config->get('self-fqdn');
} else {
    $host = "localhost";
}


// Make the log directory and files if they don't already exist.
if (!file_exists('logs/')) {
    mkdir('logs');
}
if (!file_exists('logs/server.log')) {
    fopen('logs/server.log', 'w');
}
if (!file_exists('logs/service.log')) {
    fopen('logs/service.log', 'w');
}

// Handlers
$stream = new StreamHandler('logs/server.log');
$streamService = new StreamHandler('logs/service.log');

// Processors
$gitProc = new GitProcessor();
$introProc = new IntrospectionProcessor();
$memProc = new MemoryUsageProcessor();

// Server Log
$log = new Logger("server");
$log->pushHandler($stream);
$log->pushProcessor($gitProc);
$log->pushProcessor($introProc);
$log->pushProcessor($memProc);

// Service Log
$serviceLog = new Logger("service");
$serviceLog->pushHandler($streamService);
$serviceLog->pushProcessor($gitProc);
$serviceLog->pushProcessor($introProc);
$serviceLog->pushProcessor($memProc);

// main event loop setup
$loop   = Factory::create();
$socket	= new Server($loop);

// Set up the ServiceAPI while passing in an instance of the actual Service and a logger

// TODO Replace the 'new EchoService()' with the actual service
$service = new ServiceKernel(new EchoService(new Connection(), $config), $serviceLog);

$i = 0;
// set up the listener callback on connect
$socket->on('connection', function($conn) use ($service, $log, &$i){
    // Add some information about this new connection to the log
    $log->addInfo("New connection established", array("ID" => $conn));
    $conn->on('data', function($data) use ($conn, $service, $log) {

        // Call the main function of the serviceAPI
        $result = $service->execute($data);

        // return the result of the service task
        $conn->write($result);
        // Report successful execution
        $log->addInfo("Finished work for", array("ID" => $conn));
        $conn->end();
    });
});

// listener on rxPort
$socket->listen($port, $host);

echo $name. ' is up at address '.$host.' on port ' . $port. PHP_EOL;

// go
$loop->run();
