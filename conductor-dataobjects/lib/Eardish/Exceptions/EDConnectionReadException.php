<?php
namespace Eardish\Exceptions;


class EDConnectionReadException extends EDException
{

    public function __construct($message = null, $addr = null, $port = null, $state = null)
    {
        if(!($message)) {
            $message = "unable to read data from networked resource at $addr - port $port";
        }

        parent::__construct($message, 24, $state);
    }

}