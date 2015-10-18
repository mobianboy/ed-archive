<?php
namespace Eardish\Gateway\DataObjects\DataObjects;

use Eardish\Gateway\DataObjects\Core\SingleLevelDataObject;

class MetaBlock extends SingleLevelDataObject
{
    public function __construct()
    {
        parent::__construct(
            null,
            "meta"
        );
    }

    public function setMeta($meta)
    {
        parent::setOptions($meta);
    }
}