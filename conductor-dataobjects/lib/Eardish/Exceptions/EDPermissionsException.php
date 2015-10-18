<?php
namespace Eardish\Exceptions;


class EDPermissionsException extends EDException
{

    public function __construct($message = null, $state = null)
    {
        if(!($message)) {
            $message = "credentials provided in request does not have permission to perform this action";
        }

        parent::__construct($message, 42, $state);
    }

}