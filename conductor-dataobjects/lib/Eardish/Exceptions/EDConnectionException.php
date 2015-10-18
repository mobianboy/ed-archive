<?php
namespace Eardish\Exceptions;


class EDConnectionException extends EDException
{

    public function __construct($message = null, $addr = null, $port = null, $state = null)
    {
        if(!($message)) {
            $message = "Unable to connect to networked resource at $addr - port $port";
        }

        parent::__construct($message, 22, $state);
    }

}
