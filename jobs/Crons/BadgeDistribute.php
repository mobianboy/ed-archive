<?php
require 'bootstrap.php';
require 'Crons/Core/ConfigLoader.php';
use Eardish\Tools\EDSocketConnect;
use Eardish\DataObjects\Request;
use Eardish\DataObjects\Blocks\RouteBlock;
use Eardish\DataObjects\Blocks\MetaBlock;
use Eardish\DataObjects\Blocks\ActionBlock;
use Eardish\DataObjects\Blocks\AuditBlock;

// set up route block
$routeBlock = new RouteBlock();
$routeBlock->setControllerName("Social");
$routeBlock->setControllerMethod("distributeBadges");

// set meta block
$metaBlock = new MetaBlock();

// set audit block
$auditBlock = new AuditBlock();

// set priority in action block
$actionBlock = new ActionBlock("", 10);
$actionBlock->setResponseToken("cron-badgeDistribute");

$request = new Request(
    array(
        $actionBlock,
        $routeBlock,
        $metaBlock,
        $auditBlock
    )
);
$socket = new EDSocketConnect($config->get('bridge.address'), $config->get('bridge.front.port'));
$socket->send($request);

//if ($response == 200) {
//    echo 'jobs done' . PHP_EOL;
//} else {
//    echo 'failure';
//}

