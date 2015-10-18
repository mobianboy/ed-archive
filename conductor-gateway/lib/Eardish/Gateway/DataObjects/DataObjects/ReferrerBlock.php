<?php

namespace Eardish\Gateway\DataObjects\DataObjects;

use Eardish\Gateway\DataObjects\Core\SingleLevelDataObject;
use Eardish\Exceptions\EDInvalidOrMissingParameterException;

class ReferrerBlock extends SingleLevelDataObject
{
    public function __construct()
    {
        parent::__construct(
            null,
            "referrer"
        );
    }
    /**
     *
     * @param array $options
     * @throws \InvalidArgumentException
     */
    public function setReferrer($options)
    {
        if (is_array($options)) {
            parent::setOptions($options);
        } else {
            throw new EDInvalidOrMissingParameterException("GATEWAY::ReferrerBlock:  invalid arguments");
        }
    }
}
