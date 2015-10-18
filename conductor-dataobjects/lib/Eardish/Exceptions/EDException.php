<?php
namespace Eardish\Exceptions;

/**
 * Class EDException
 */

class EDException extends \Exception
{
    protected $state;

    public function __construct($message = null, $code = 21, $state = null)
    {
        $this->state = $state;
        if(!($message)) {
            $message = "general error";
        }
        parent::__construct($message, $code);
    }

    public function getState()
    {
        return $this->state;
    }

}
