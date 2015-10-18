<?php
$username = 'eardish';
$password = 'password';
$host = 'localhost';
$port = '5432';
$dbname = 'eardish-cron';


$pgConn = pg_pconnect("dbname=$dbname port=$port user=$username password=$password port=$port");

$query = "CREATE TABLE cron_queue (
    id SERIAL PRIMARY KEY,
    label varchar(30) NOT NULL,
    operation varchar(30) NOT NULL,
    data text NOT NULL,
    status integer);";


pg_query($pgConn, $query);