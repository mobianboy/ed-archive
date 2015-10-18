<?php

namespace Eardish\Exceptions;


class EDMissingActionException extends EDException
{

    public function __construct($message = null, $state = null)
    {
        if(!($message)) {
            $message = "request contains no action";
        }

        parent::__construct($message, 10, $state);
    }
}