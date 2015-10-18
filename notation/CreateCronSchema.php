<?php
require 'bootstrap.php';


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

$address = $config->get('databases.postgre.address');
$port = $config->get('databases.postgre.port');
$username = $config->get('databases.postgre.username');
$password = $config->get('databases.postgre.password');
$dbname = 'eardishcron';

$pgConn = pg_pconnect('dbname='.$dbname.' host='.$port.' user='.$username.' password='.$password.' host='. $address);

$query = "CREATE TABLE cron_queue (
    id SERIAL PRIMARY KEY,
    label varchar(30) NOT NULL,
    operation varchar(30) NOT NULL,
    data text NOT NULL,
    status integer);";

pg_query($pgConn, $query);