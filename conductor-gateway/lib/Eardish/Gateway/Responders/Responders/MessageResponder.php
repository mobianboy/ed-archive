<?php
namespace Eardish\Gateway\Responders\Responders;

use Eardish\Gateway\DataObjects\DataObjects\MetaBlock;
use Eardish\Gateway\DataObjects\DataObjects\DataBlock;
use Eardish\Gateway\DataObjects\DataObjects\MessageBlock;
use Eardish\Gateway\DataObjects\DataObjects\StatusCode;
use Eardish\Gateway\Responders\Core\BasicResponder;

class MessageResponder extends BasicResponder
{
    public function __construct($statusCode, $messageData, $metaData = null, $dataData = null)
    {
        $status = new StatusCode();
        $status->setCode($statusCode);
        $this->data['status'] = $status;
        // Meta block will go here once it's done.
        $message = new MessageBlock();
        $message->setMessage($messageData);
        $this->data['message'] = $message;

        if ($dataData != null) {
            $data = new DataBlock();
            $data->setData($dataData);
            $this->data['data'] = $data;
        }

        if ($metaData != null) {
            $meta = new MetaBlock();
            $meta->setMeta($metaData);
            $this->data['meta'] = $meta;
        }
    }
}
