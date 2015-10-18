<?php
require 'bootstrap.php';

use Doctrine\DBAL\DriverManager;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;

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

$config = new \Eardish\AppConfig('/eda/secret/app.json', $cli->arguments->get('env'));

$configFile = '/eda/secret/pws.json';
$secretConfig = new \Eardish\AppConfig($configFile, $cli->arguments->get('env'));

$address = $config->get('notation.databases.postgre.address');
$port = $config->get('notation.databases.postgre.port');
$username = $secretConfig->get('notation.databases.postgre.username');
$password = $secretConfig->get('notation.databases.postgre.password');

$db = DriverManager::getConnection(array(
    'dbname' => 'eardish',
    'user' => $username,
    'password' => $password,
    'host' => $address,
    'driver' => 'pdo_pgsql',
));

$dbal = new \Eardish\DatabaseService\DatabaseControllers\PostgresController(
    new \Eardish\DatabaseService\CronConnection(
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

$helperSet = new HelperSet(array(
    'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($db),
    'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($entityManager)
));

chdir(__DIR__);

$console = new Application;
$console->setHelperSet($helperSet);
$console->addCommands(array(
    new \Eardish\DatabaseService\Migration\MigrationDiff(),
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\ExecuteCommand,
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\GenerateCommand,
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand,
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\StatusCommand,
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\VersionCommand,
));

$console->run();
