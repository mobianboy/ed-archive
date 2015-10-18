<?php
namespace Eardish\Gateway\DataObjects\DataObjects;

use Eardish\Gateway\DataObjects\Core\SingleLevelDataObject;
use \Eardish\Exceptions\EDException;
use Eardish\Exceptions\EDInvalidOrMissingParameterException;

class MessageBlock extends SingleLevelDataObject
{
    public function __construct()
    {
        parent::__construct(
            null,
            "message"
        );
    }
    public function setMessage($options)
    {
        if (is_array($options)) {
            parent::setOptions($options);
        } else {
            throw new EDInvalidOrMissingParameterException("GATEWAY::MessageBlock:  invalid argument (must be an array)");
        }
    }
}
