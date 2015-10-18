<?php
require 'bootstrap.php';

$cronService = new \Eardish\Cron\CronService();
$cronService->getEntryFromDB();