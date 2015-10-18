<?php
namespace Eardish\Gateway\Responders\Responders;

use Eardish\Gateway\DataObjects\DataObjects\MetaBlock;
use Eardish\Gateway\Responders\Core\BasicResponder;
use Eardish\Gateway\DataObjects\DataObjects\StatusCode;
use Eardish\Gateway\DataObjects\DataObjects\DataBlock;

class DataResponder extends BasicResponder
{
    /**
     * @param $dataArray
     * @param $meta
     * @param $statusCode
     */
    public function __construct($dataArray, $statusCode, $meta = null)
    {
        $status = new StatusCode();
        $status->setCode($statusCode);
        $this->data['status'] = $status;

        if($meta != null) {
            $metaBlock = new MetaBlock();
            $metaBlock->setMeta($meta);
            $this->data['meta'] = $metaBlock;
        }

        $dataBlock = new DataBlock();
        if (isset($meta['listType'])) {
            $dataBlock->setData($dataArray, $meta['listType']);
        } else {
            $dataBlock->setData($dataArray);
        }

        $this->data['data'] = $dataBlock;

    }
}
