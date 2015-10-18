<?php

require 'bootstrap.php';
require 'vendor/autoload.php';
require 'errorExceptions.php';

use Eardish\Gateway\GatewayKernel;
use React\EventLoop\Factory;
use Eardish\Gateway\Socket\Server;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Eardish\Gateway\Socket\Verifier;
use Eardish\Gateway\Socket\Handshake;
use Eardish\Gateway\Socket\Frames\Framer;
use Eardish\DataObjects\Blocks;
use Eardish\DataObjects\Blocks\MetaBlock;
use Eardish\DataObjects\Response;

const CONFIG_SERVICE_NAME = 'gateway';

// Name used for other output
const ACTUAL_SERVICE_NAME = 'Conductor Gateway';

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

$webPort   = $config->get(CONFIG_SERVICE_NAME.'.web-port');
$servicePort   = $config->get(CONFIG_SERVICE_NAME.'.service-port');
$backPort = $config->get(CONFIG_SERVICE_NAME.'.back-port');

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

//$newRelic = new \Monolog\Handler\NewRelicHandler(Logger::INFO, true, 'IntDev Gateway', true);

// Processors
$gitProc = new \Monolog\Processor\GitProcessor();
$introProc = new \Monolog\Processor\IntrospectionProcessor();
$memProc = new \Monolog\Processor\MemoryUsageProcessor();

// TODO: Provide loggers with server specific configs

// Server Log
$log = new Logger('server');
//$log->pushHandler($newRelic);
$log->pushHandler($stream);
$log->pushProcessor($gitProc);
$log->pushProcessor($introProc);
$log->pushProcessor($memProc);

// API Log
$apiLog = new Logger('api');
//$apiLog->pushHandler($newRelic);
$apiLog->pushHandler($stream);
$apiLog->pushProcessor($gitProc);
$apiLog->pushProcessor($introProc);
$apiLog->pushProcessor($memProc);

// Start up React and prep the servers
$loop = Factory::create();
$web = new Server($loop);
$service = new Server($loop);
$back = new Server($loop);

/**
 * @var $api GatewayKernel
 */
$api = new GatewayKernel($apiLog, $config, $host);
//
set_error_handler(function ($err_severity, $err_msg, $err_file, $err_line, array $err_context) use ($apiLog)
{
    // error was suppressed with the @-operator
    try {
        if (0 === error_reporting()) { return false;}
        switch($err_severity)
        {
            case E_ERROR:               throw new ErrorException            ($err_msg, 20, $err_severity, $err_file, $err_line);
            case E_WARNING:             throw new WarningException          ($err_msg, 20, $err_severity, $err_file, $err_line);
            case E_PARSE:               throw new ParseException            ($err_msg, 20, $err_severity, $err_file, $err_line);
            case E_NOTICE:              throw new NoticeException           ($err_msg, 20, $err_severity, $err_file, $err_line);
            case E_CORE_ERROR:          throw new CoreErrorException        ($err_msg, 20, $err_severity, $err_file, $err_line);
            case E_CORE_WARNING:        throw new CoreWarningException      ($err_msg, 20, $err_severity, $err_file, $err_line);
            case E_COMPILE_ERROR:       throw new CompileErrorException     ($err_msg, 20, $err_severity, $err_file, $err_line);
            case E_COMPILE_WARNING:     throw new CoreWarningException      ($err_msg, 20, $err_severity, $err_file, $err_line);
            case E_USER_ERROR:          throw new UserErrorException        ($err_msg, 20, $err_severity, $err_file, $err_line);
            case E_USER_WARNING:        throw new UserWarningException      ($err_msg, 20, $err_severity, $err_file, $err_line);
            case E_USER_NOTICE:         throw new UserNoticeException       ($err_msg, 20, $err_severity, $err_file, $err_line);
            case E_STRICT:              throw new StrictException           ($err_msg, 20, $err_severity, $err_file, $err_line);
            case E_RECOVERABLE_ERROR:   throw new RecoverableErrorException ($err_msg, 20, $err_severity, $err_file, $err_line);
            case E_DEPRECATED:          throw new DeprecatedException       ($err_msg, 20, $err_severity, $err_file, $err_line);
            case E_USER_DEPRECATED:     throw new UserDeprecatedException   ($err_msg, 20, $err_severity, $err_file, $err_line);

            default:
                $apiLog->addWarning('Unknown Error', array(
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
            'message' => $exception->getMessage(),
            'trace' => $traceArr[1],
        );
        $apiLog->addWarning("Exception Thrown", $context);
    }

    return true;
});

// Server errors
$web->on('error', function($errorArray) use ($log) {
    $log->addError('An error occurred on the outward facing server.', $errorArray);
});

$service->on('error', function($errorArray) use ($log) {
    $log->addError('An error occurred on the inward facing server.', $errorArray);
});

$requestId = 0;
// Add event triggers
$web->on('connection', function(Eardish\Gateway\Socket\Connection $conn) use ($api, $log, $apiLog, &$requestId) {
    // Add the connection to the API
    $connId = $api->newConnection($conn);
    // Log the connection
    // $log->addInfo("New connection established", array("ID" => $connId));
    // set up data handler
    $conn->on('data', function($data) use ($api, $conn, $connId, $log, $apiLog, &$requestId) {
        $reqId = ++$requestId;
        if (!$conn->isUpgraded()) {
            $verifier = new Verifier($data);
            if ($verifier->isValidUpgrade()) {
                $handshake = new Handshake($verifier->getWSKey());
                $conn->write($handshake->response());
                $conn->upgrade();
            } else {
                $conn->end();
            }
            unset($verifier, $handshake);
        } else {
            // check to see if there is a read in progress - if not, create new framer instance
            /**
             * @var $framer Framer
             */
            $framer = $conn->getFramer();
            if(isset($framer)) {
                if(!($framer->partialFrameRead())) {
                 //   $framer->insertBufferOverread($conn->getOverreadBuffer());
                    // if any buffer is left unused, save it in connection for the
                    // next read

                    unset($framer);
                }
            }

            if(!(isset($framer))) {
                $framer = new Framer();
                // keep some state in case of some
                $conn->setFramer($framer);
                // put the buffer overread from last data read into the framer
                $framer->setBufferOverread($conn->getOverreadBuffer());
            }

            // $framer = new Framer();
            $decodedData = $framer->decode($data);

            /*
             * Check to see if decoding completed successfully or if a close frame was received (or other errors)
             * TODO: Implement function that more reliably checks decoding result cases in Framer and update this `if` statement
             */
            if ($decodedData) {
                // check for decoding errors before handling data
                // if there is a decoding error, $decodedData will
                // contain an error code and message to be sent to the
                // client before closing the connection.
                // return status codes are per RFC6455
                //
                if($framer->error()!=1000) {
                    $conn->write($decodedData);
                //    $log->addError("client connection terminated with an error code of " . $framer->error());
                    $conn->end();

                // we also check for normal close frames and act accordingly
                } elseif($framer->isClose()) {
                    $conn->write($decodedData);
                //    $log->addError("client terminated the connection normally");
                    $conn->end();
                // looks okay, send it down
                } else {
                    try
                    {
                        $api->handle($decodedData, $connId, $reqId);
                    }
                    catch (\Exception $ex)
                    {
                        $request = json_decode($decodedData, true);
                        if (isset($request['action']['responseToken'])) {
                            $meta = new MetaBlock();
                            $meta->setResponseToken($request['action']['responseToken']);
                        } else {
                            $meta = null;
                        }
                        // create audit block (cannot pull from DTO here)
                        // send exception message to builder
                        $ab = new Blocks\AuditBlock();
                        $ab->addException($ex);
                        $responseObject = new Response([$ab, $meta]);
                        $conn->write($api->getBuilder()->buildResponder($responseObject));
                        // clean up resources
                        $conn->releaseFramer();
                        unset($framer);

                        $traceArr = explode("\n", $ex->xdebug_message);
                        //log all exceptions thrown
                        $context = array(
                            'code' => $ex->getCode(),
                           // 'message' => $ex->getMessage(),
                            'trace' => $traceArr[1]
                        );
                        $apiLog->addWarning('Exception Thrown', $context);

                        // return from the event handler
                        return false;
                    }
                }
            } else {
                // if null is received, chances are it means the operation needs more data
                // otherwise, if not a partial frame read, then something went wrong
                if(!($framer->partialFrameRead())) {
                    $conn->end();
                    $conn->releaseFramer();
                    unset($framer, $decodedData);
                }
            }
        }
    });


//    $conn->on('close', function() use ($api, $connId) {
//        $api->killConnection($connId);
//    });

    $conn->on('end', function() use ($api, $connId, $conn) {
        $api->killConnection($connId, $conn);
    });

    // log connection errors
    $conn->on('error', function($errorArray) use ($log, $conn) {
        $log->addError('An error occurred on connection '.$conn->getResourceId().' connected to the outward facing server.', $errorArray);
    });
});

$service->on('connection', function(Eardish\Gateway\Socket\Connection $conn) use ($api, $log) {
    $conn->on('data', function($data) use ($api, $conn, $log) {
        try {
            $api->build($data, $conn);

        } catch (\Exception $e) {
            if (($e instanceof TransportException)) {
                $log->addEmergency("GATEWAY::Error occurred while unserializing request");
            } else {
                //TODO figure out a way to get client connID and write back
            }
        }
        $conn->end();
    });

    // log connection errors
    $conn->on('error', function($errorArray) use ($log, $conn) {
        $log->addError('An error occurred on connection '.$conn->getResourceId().' connected to the inward facing server.', $errorArray);
    });
});

$back->on('connection', function(Eardish\Gateway\Socket\Connection $conn) use ($api, $log) {
    $conn->on('data', function($data) use ($api, $conn, $log) {
        $data = json_decode($data, true);
        try {
            $api->next($data);

        } catch (\Exception $ex) {
            $requestId = $data['requestId'];
            $api->processException($ex, $requestId);

            if (isset($request['action']['responseToken'])) {
                $meta = new MetaBlock();
                $meta->setResponseToken($request['action']['responseToken']);
            } else {
                $meta = null;
            }
            // create audit block (cannot pull from DTO here)
            // send exception message to builder
            $ab = new Blocks\AuditBlock();
            $ab->addException($ex);
            $responseObject = new Response([$ab, $meta]);
            $conn->write($api->getBuilder()->buildResponder($responseObject));
            // clean up resources
            $conn->releaseFramer();
            unset($framer);

            $traceArr = explode("\n", $ex->xdebug_message);
            //log all exceptions thrown
            $context = array(
                'code' => $ex->getCode(),
                // 'message' => $ex->getMessage(),
                'trace' => $traceArr[1]
            );
            $log->addWarning('Exception Thrown', $context);

            // return from the event handler
            return false;
        }
        $conn->end();
    });

    // log connection errors
    $conn->on('error', function($errorArray) use ($log, $conn) {
        $log->addError('An error occurred on connection '.$conn->getResourceId().' connected to the inward facing server.', $errorArray);
    });
});

$monitorPort = $config->get(CONFIG_SERVICE_NAME.'.monitor.port');
$monitor = new Server($loop);

$monitor->on('connection', function ($conn) {
    $conn->write('Successful check:');
    $conn->end();
});
$monitor->listen($monitorPort, $host);

// use a different port for testing.
$service->listen($servicePort, $host);
$web->listen($webPort, $host);
$back->listen($backPort, $host);
$cli->bold()->green(ACTUAL_SERVICE_NAME . ' is up at address '.$host.' on ports ' . $webPort . '(web port) and ' . $servicePort . '(service port) :: Monitoring on port ' . $config->get(CONFIG_SERVICE_NAME.'.monitor.port'));

$loop->run();