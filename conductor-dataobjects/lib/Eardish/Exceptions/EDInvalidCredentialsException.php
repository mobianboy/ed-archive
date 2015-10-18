<?php
namespace Eardish\Exceptions;


class EDInvalidCredentialsException extends EDException
{

    public function __construct($message = null, $state = null)
    {
        if(!($message)) {
            $message = "request contained invalid credentials";
        }

        parent::__construct($message, 41, $state);
    }

}