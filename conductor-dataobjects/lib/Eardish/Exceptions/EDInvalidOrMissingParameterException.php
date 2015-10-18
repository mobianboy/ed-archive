<?php
namespace Eardish\Exceptions;


class EDInvalidOrMissingParameterException extends EDException
{

    public function __construct($message = null, $state = null)
    {
        if(!($message)) {
            $message = "request contained invalid, incorrect, missing or incomplete parameters";
        }

        parent::__construct($message, 11, $state);
    }

}