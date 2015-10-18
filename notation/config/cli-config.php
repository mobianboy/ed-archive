<?php
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Eardish\DatabaseService\DatabaseControllers\PostgresController;
use Eardish\DatabaseService\CronConnection;

// replace with file to your own project bootstrap
require 'bootstrap.php';

$env = file_get_contents(__DIR__."/env.txt");
if (empty($env)) {
    echo 'env.txt is empty. edit the file with the env or run the service with the appropriate flag and try again.'. PHP_EOL;
    die();
}
$configFile = '/eda/secret/app.json';
$config = new \Eardish\AppConfig($configFile, $env);

$configFile = '/eda/secret/pws.json';
$secretConfig = new \Eardish\AppConfig($configFile, $env);

$address = $config->get('notation.databases.postgre.address');
$port = $config->get('notation.databases.postgre.port');
$username = $secretConfig->get('notation.databases.postgre.username');
$password = $secretConfig->get('notation.databases.postgre.password');

$dbal = new PostgresController(
    new CronConnection(
        $address,
        $port,
        $username,
        $password
    ),
    $address,
    $port,
    $username,
    $password
);
// replace with mechanism to retrieve EntityManager in your app
$entityManager = $dbal->getEntityManager();

return ConsoleRunner::createHelperSet($entityManager);
