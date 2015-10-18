<?php
namespace Eardish\Exceptions;


class EDTransportException extends EDException
{

    public function __construct($message = null, $addr = null, $port = null, $state = null)
    {
        if(!($message)) {
            $message = "Unable to send data to networked resource at $addr - port $port";
        }

        parent::__construct($message, 25, $state);
    }

}