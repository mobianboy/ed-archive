<?php

require('bootstrap.php');
require('vendor/autoload.php');

use Eardish\SongIngestionService\ServiceKernel;
use Eardish\SongIngestionService\SongIngestionService;
use Eardish\SongIngestionService\Core\Connection;
use Eardish\SongIngestionService\SongProcessors\WaveformGenerator;
use Eardish\SongIngestionService\SongProcessors\MetadataProcessor;
use Eardish\SongIngestionService\SongProcessors\SongTranscoder;
use React\Socket\Server;
use React\EventLoop\Factory;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\MemoryUsageProcessor;
use Monolog\Processor\GitProcessor;

use \Eardish\Exceptions\EDException;

// MONOLOG

const CONFIG_SERVICE_NAME = 'songingestion';

// Name used for other output
const ACTUAL_SERVICE_NAME = 'Song Ingestion Service';

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

$env = $cli->arguments->get('env');

$configFile = '/eda/secret/app.json';

$config = new \Eardish\AppConfig($configFile, $cli->arguments->get('env'));
// default port

$port   = $config->get(CONFIG_SERVICE_NAME.'.port');

if ($env != 'local') {
    if (!file_exists($config->get('fqdn-path'))) {
        $cli->red()->out('ATTENTION: '.$config->get('fqdn-path').' was not found. Terminating.');
        die();
    }
    $host = trim(file_get_contents($config->get('fqdn-path')));
} else {
    $host = 'localhost';
}

// MONOLOG
//Check where to save logs, depending on environment

$localLog = "logs/local-".CONFIG_SERVICE_NAME.".log";
$nonLocalLog = "/eda/logs/$env-".CONFIG_SERVICE_NAME.".log";

//Handlers, dependent on environment
if ($env == 'local') {
    $stream = new StreamHandler($localLog);
} else {
    $stream = new StreamHandler($nonLocalLog);
}

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
$serviceLog->pushHandler($stream);
$serviceLog->pushProcessor($gitProc);
$serviceLog->pushProcessor($introProc);
$serviceLog->pushProcessor($memProc);

// main event loop setup
$loop   = Factory::create();
$socket	= new Server($loop);

// set up the notice to exception handler
set_error_handler(function ($err_severity, $err_msg, $err_file, $err_line, array $err_context) use ($serviceLog)
{
    // error was suppressed with the @-operator
    try {
        if (0 === error_reporting()) { return false;}
        switch($err_severity)
        {
            case E_ERROR:               throw new EDException               ($err_msg, 20);
            case E_WARNING:             throw new EDException               ($err_msg, 20);
            case E_PARSE:               throw new EDException               ($err_msg, 20);
            case E_NOTICE:              throw new EDException               ($err_msg, 20);
            case E_CORE_ERROR:          throw new EDException               ($err_msg, 20);
            case E_CORE_WARNING:        throw new EDException               ($err_msg, 20);
            case E_COMPILE_ERROR:       throw new EDException               ($err_msg, 20);
            case E_COMPILE_WARNING:     throw new EDException               ($err_msg, 20);
            case E_USER_ERROR:          throw new EDException               ($err_msg, 20);
            case E_USER_WARNING:        throw new EDException               ($err_msg, 20);
            case E_USER_NOTICE:         throw new EDException               ($err_msg, 20);
            case E_STRICT:              throw new EDException               ($err_msg, 20);
            case E_RECOVERABLE_ERROR:   throw new EDException               ($err_msg, 20);
            case E_DEPRECATED:          throw new EDException               ($err_msg, 20);
            case E_USER_DEPRECATED:     throw new EDException               ($err_msg, 20);

            default:
                $serviceLog->addWarning('Unknown Error', array(
                    'error_message' => $err_msg,
                    'error_severity' => $err_severity,
                    'error_file' => $err_file,
                    'error_line' => $err_line
                ));
        }
    } catch (\Exception $exception) {
        $traceArr = explode("\n", $exception->xdebug_message);
        $context = array(
            'code' => $exception->getCode(),
            'trace' => $traceArr[1]
        );
        $serviceLog->addWarning("Custom Exception Thrown", $context);
    }
    return true;
});

// Set up the ServiceAPI while passing in an instance of the actual Service and a logger
 $service = new ServiceKernel(new SongIngestionService(new Connection(),
//    new WaveformGenerator(),
//    new MetadataProcessor(),
//    new SongTranscoder(),
    $config),
    $serviceLog);

$i = 0;
// set up the listener callback on connect
$socket->on('connection', function($conn) use ($service, $log, $serviceLog, &$i){
    // Add some information about this new connection to the log
    $log->addInfo("New connection established", array("ID" => $conn));
    $conn->on('data', function($data) use ($conn, $service, $log, $serviceLog) {
        try {
            // Call the main function of the serviceAPI
            $result = $service->execute($data);
            // return the result of the service task
            $conn->write(json_encode($result));
        } catch (\Exception $ex) {
            $exceptionArray = array();
            $exceptionArray["exception"]["code"]    = $ex->getCode();
            $exceptionArray["exception"]["message"] = $ex->getMessage();

            $traceArr = explode("\n", $ex->xdebug_message);
            $context = array(
                'code' => $ex->getCode(),
                'trace' => $traceArr[1]
            );
            $serviceLog->addWarning('Exception Thrown', $context);

            $conn->write(json_encode($exceptionArray));
        }
        // Report successful execution
        $log->addInfo("Finished work for", array("ID" => $conn));
        $conn->end();
    });
});

$monitorPort = $config->get(CONFIG_SERVICE_NAME.'.monitor.port');
$monitor = new Server($loop);

$monitor->on('connection', function ($conn) {
    $conn->write('Successful check:');
    $conn->end();
});
$monitor->listen($monitorPort, $host);

// listener on rxPort
$socket->listen($port, $host);

$cli->bold()->green(ACTUAL_SERVICE_NAME . ' is up at address '.$host.' on port ' . $port . ' :: Monitoring on port ' . $config->get(CONFIG_SERVICE_NAME.'.monitor.port'));

// go
$loop->run();
