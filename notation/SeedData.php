<?php
require 'bootstrap.php';

use Eardish\DatabaseService\DatabaseControllers\Models;
use Eardish\DatabaseService\DatabaseControllers\SeedControllers\Seeders\ElasticSeeder;
use Eardish\DatabaseService\DatabaseControllers\SeedControllers\Seeders\NeoSeeder;
use Eardish\DatabaseService\DatabaseControllers\SeedControllers\Seeders\PostgresSeeder;
use Eardish\DatabaseService\DatabaseControllers\SeedControllers\Seeders\BonJoviSeeder;

/**
 * Run the functions
 */
$numElements = 100;

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

$configFile = '/eda/secret/app.json';

$env = $cli->arguments->get('env');

$config = new \Eardish\AppConfig($configFile, $cli->arguments->get('env'));
$configFile = '/eda/secret/pws.json';
$secretConfig = new \Eardish\AppConfig($configFile, $cli->arguments->get('env'));


$postgresSeeder = new PostgresSeeder($numElements, $config, $secretConfig);
$postgresSeeder->seed();
//$bonJovi = new BonJoviSeeder($numElements);
//$bonJovi->seed();

//$neoSeeder = new NeoSeeder($numElements);
//$neoSeeder->seed();
//
//$elasticSeeder = new ElasticSeeder($numElements);
//$elasticSeeder->seed();

