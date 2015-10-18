<?php
namespace Eardish\Gateway\Responders\Responders;

use Eardish\Gateway\DataObjects\DataObjects\StatusCode;
use Eardish\Gateway\Responders\Core\BasicResponder;
use Eardish\Gateway\DataObjects\DataObjects\MetaBlock;

class NullResponder extends BasicResponder
{
    /**
     * @param $exception
     * @param array $meta
     */
    public function __construct($exception, $meta = null)
    {
        $status = new StatusCode();
        if (is_array($exception)){
            if (array_key_exists('message', $exception)) {
            $status->setCode($exception["code"], $exception["message"]);
            }
        } elseif (is_int($exception)) {
            $status->setCode($exception, null);
        }
        $this->data['status'] = $status;

        if($meta) {
            $metaBlock = new MetaBlock();
            $metaBlock->setMeta($meta);
            $this->data['meta'] = $metaBlock;
        }
    }
}
