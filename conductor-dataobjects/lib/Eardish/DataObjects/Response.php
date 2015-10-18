<?php
namespace Eardish\DataObjects;

use Eardish\DataObjects\Core\AbstractDataObject;

class Response extends AbstractDataObject implements \JsonSerializable
{
    public function objectToArray($block = null)
    {
        $result = array('data' => array(), 'meta' => array(), 'followUp' => array(), 'message' => array(), 'status' => array());

        if ($block) {
            $function = "build". $block . "Array";
            $this->$function($result);

            return $result[$block];
        } else {
            if ($this->blockExists('data')) {
                $this->buildDataArray($result);
            }

            if ($this->blockExists('status')) {
                $this->buildStatusArray($result);
            }

            if ($this->blockExists('meta')) {
                $this->buildMetaArray($result);
            }

            if ($this->blockExists('followUp')) {
                $this->buildFollowUpArray($result);
            }

            if ($this->blockExists('message')) {
                $this->buildMessageArray($result);
            }

            return $result;
        }
    }

    protected function buildMetaArray(&$result)
    {
        // Set meta array
        $this->buildIfSet($result['meta'], array(
            'currentUser' => $this->metaBlock->getCurrentUser(),
            'currentProfile' => $this->metaBlock->getCurrentProfile(),
            'connId' => $this->metaBlock->getConnId(),
            'apiVersion' => $this->metaBlock->getApiVersion(),
            'dataSource' => $this->metaBlock->getDataSource(),
            'modelType' => $this->metaBlock->getModelType(),
            'responseToken' => $this->metaBlock->getResponseToken(),
            'listType' => $this->metaBlock->getListType(),
            'invalidFields' => $this->metaBlock->getInvalidFields()
        ));
    }

    protected function buildDataArray(&$result)
    {
        $this->buildIfSet($result, array(
            'data' => $this->dataBlock->getDataArray()
        ));
    }

    protected function buildStatusArray(&$result)
    {
        $this->buildIfSet($result['status'], array(
            'code' => $this->statusBlock->getCode(),
            'message' => $this->statusBlock->getMessage()
        ));
    }

    protected function buildFollowUpArray(&$result)
    {
        $this->buildIfSet($result['followUp'], array(
            'type' => $this->followUpBlock->getType(),
            'url' => $this->followUpBlock->getUrl(),
            'format' => $this->followUpBlock->getFormat()
        ));
    }

    protected function buildMessageArray(&$result)
    {
        $this->buildIfSet($result['message'], array(
            'message' => $this->messageBlock->getMessages()
        ));
    }

    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     */
    function jsonSerialize()
    {
        return $this->objectToArray();
    }
}