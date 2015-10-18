<?php

require 'bootstrap.php';
require 'vendor/autoload.php';
require 'errorExceptions.php';

use Eardish\Bridge\BridgeKernel;
use React\EventLoop\Factory;
use React\Socket\Server;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use \Eardish\Bridge\Agents\Core\Connection;
use Eardish\DataObjects\Blocks\AuditBlock;
use \Eardish\Exceptions\EDException;

/**
 * Bridge server script
 *
 *
 */
///// BEGIN OPTIONS SET (refactor candidate)

// Name used to load configuration from app.json
// Refer to app.json to see the appropriate name for this.
const CONFIG_SERVICE_NAME = 'bridge';

// Name used for other output
const ACTUAL_SERVICE_NAME = 'Conductor Bridge';

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

$frontPort   = $config->get(CONFIG_SERVICE_NAME.'.front.port');
$backPort   = $config->get(CONFIG_SERVICE_NAME.'.back.port');

if ($env != 'local') {
    if (!file_exists($config->get('fqdn-path'))) {
        $cli->red()->out('ATTENTION: '.$config->get('fqdn-path').' was not found. Terminating.');
        die();
    }
    $host = trim(file_get_contents($config->get('fqdn-path')));
} else {
    $host = 'localhost';
}

$config->safeSet('fqdn', $host);

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
$gitProc = new \Monolog\Processor\GitProcessor();
$introProc = new \Monolog\Processor\IntrospectionProcessor();
$memProc = new \Monolog\Processor\MemoryUsageProcessor();

// Server Log
$log = new Logger("server");
$log->pushHandler($stream);
$log->pushProcessor($gitProc);
$log->pushProcessor($introProc);
$log->pushProcessor($memProc);

// Bridge Log
$bridgeLog = new Logger("bridge");
$bridgeLog->pushHandler($stream);
$bridgeLog->pushProcessor($gitProc);
$bridgeLog->pushProcessor($introProc);
$bridgeLog->pushProcessor($memProc);

// create directory for logs
if (!file_exists('logs/')) {
    mkdir('logs');
}

// main event loop setup
$loop       = Factory::create();
$front	    = new Server($loop);
$back	    = new Server($loop);
$sm         = new BridgeKernel(new Connection(), $config, $bridgeLog);

set_error_handler(function ($err_severity, $err_msg, $err_file, $err_line, array $err_context) use ($bridgeLog)
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
                $bridgeLog->addWarning('Unknown Error', array(
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
        $bridgeLog->addWarning("Exception Thrown", $context);
    }
    return true;
});

$front->on('error', function($errorArray) use ($log) {
    $log->addError('An error occurred on the server.', $errorArray);
});

// set up the listener callback on connect

$counter = 0;
$front->on('connection', function($conn) use ($sm, $log, $bridgeLog, &$counter) {
    // set up data handler
    $log->addInfo("CONNECT::");
    $conn->on('data', function($data) use ($sm, $conn, $log, $bridgeLog, &$counter) {
        $requestId = ++$counter;
        // marshal payload data - convert to DTO object
        try {
            $dto = $sm->unserialize($data);
            return $sm->inbound($dto, $requestId);
        } catch (\Exception $e) {
            $sm->processException($e, $requestId);
        }
    });
});

$back->on('connection', function($conn) use ($sm, $log, $bridgeLog) {
    $conn->bufferSize = 1000000;
    // set up data handler
    $log->addInfo("CONNECT::");
    $conn->on('data', function($data) use ($sm, $conn, $log, $bridgeLog) {
        $data = json_decode($data, true);
        try {
            if (array_key_exists('exception', $data)) {
                throw new EDException($data['exception']['message'], $data['exception']['code']);
            }
            $sm->next($data);
        } catch (\Exception $e) {
            $requestId = $data['requestId'];
            $sm->processException($e, $requestId);
        }
    });
});

$back->on('error', function($errorArray) use ($log) {
    $log->addError('An error occurred on the server.', $errorArray);
});

$monitorPort = $config->get(CONFIG_SERVICE_NAME.'.monitor.port');
$monitor = new Server($loop);

$monitor->on('connection', function ($conn) {
    $conn->write('Successful check:');
    $conn->end();
});
$monitor->listen($monitorPort, $host);

$front->listen($frontPort, $host);
$back->listen($backPort, $host);

$cli->bold()->green(ACTUAL_SERVICE_NAME . ' is up at address '.$host.' on front port: ' . $frontPort . ', back port: '. $backPort .' :: Monitoring on port ' . $config->get(CONFIG_SERVICE_NAME.'.monitor.port'));
// go
$loop->run();