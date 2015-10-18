<?php
namespace Eardish\Exceptions;


class EDMissingCredentialsException extends EDException
{

    public function __construct($message = null, $state = null)
    {
        if(!($message)) {
            $message = "request contained no required credentials";
        }

        parent::__construct($message, 40, $state);
    }

}