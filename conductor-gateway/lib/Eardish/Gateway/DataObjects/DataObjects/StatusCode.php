<?php

namespace Eardish\Gateway\DataObjects\DataObjects;

use Eardish\Gateway\DataObjects\Core\SingleLevelDataObject;

class StatusCode extends SingleLevelDataObject
{
    protected $messages;

    public function __construct()
    {
        parent::__construct(
            null,
            "status"
        );
        // This may want to be moved out of this class at some point, probably don't want this being done everything there
        // is a null responder.
        $this->messages = $this->loadStructure(null, '/../../config', 'config');
    }

    /**
     *
     * @param int $code
     * @throws \InvalidArgumentException
     */
    public function setCode($code, $message = null)
    {
        parent::__call("setCode", array($code));

        if (!$message) {
            parent::setOption('message', $this->messages['statusBlockCodesMessages'][$this->getOption('code')]);
        } else {
            parent::setOption('message', $message);
        }
    }
}
