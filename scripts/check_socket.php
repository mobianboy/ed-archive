<?php


$errno = 0;
$errstr = '';

if (stream_socket_client('tcp://localhost:8999', $errno, $errstr) === false) {
    echo "Failure Type $errno: $errstr";
    exit(255);
} else {
    exit(0);
}