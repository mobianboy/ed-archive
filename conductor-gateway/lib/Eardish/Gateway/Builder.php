<?php
namespace Eardish\Gateway;

use Eardish\DataObjects\Blocks\StatusBlock;
use Eardish\Gateway\Responders\Core\BasicResponder;
use Eardish\Gateway\Responders\Responders\DataResponder;
use Eardish\Gateway\Responders\Responders\MessageResponder;
use Eardish\Gateway\Responders\Responders\NullResponder;
use Eardish\Gateway\Formats\Factories\JSONFactory;
use Eardish\Gateway\Socket\Frames\Framer;
use Eardish\DataObjects\Response;
use Eardish\DataObjects\Blocks\AuditBlock;
use Monolog\Logger;

class Builder
{
    /**
     * @var Response
     */
    protected $data;

    /**
     * @var Framer
     */
    protected $framer;

    /**
     * @var factory
     */
    protected $factory;

    /**
     * @var Logger
     */
    protected $logger;

    public function __construct($logger)
    {
        $this->factory = new JSONFactory();
        $this->framer = new Framer();
        $this->logger = $logger;
    }

    /**
     * Main function. Send a Response object, auditblock, or array to parse that into the appropriate responder.
     *
     * @param mixed $responseData
     * @return bool|string
     */
    public function buildResponder($responseData)
    {
        switch($responseData) {
            case ($responseData instanceof AuditBlock):
                if (($responseData->hasExceptions())) {
                    return $this->handleException($responseData);
                }

                break;
            case ($responseData instanceof Response):
                if ($responseData->blockExists('audit') && $responseData->getAuditBlock()->hasExceptions()) {
                    if ($responseData->blockExists('meta')) {
                        $meta = $responseData->objectToArray('meta');
                        $except = $this->handleException($responseData->getAuditBlock(), $meta);
                    } else {
                        $except = $this->handleException($responseData->getAuditBlock());
                    }

                    return $except;
                }
                $responder = $this->handleResponseObject($responseData);

                break;
            case (is_array($responseData)):
                $responder = $this->packageResponder($responseData);
	            
                break;
            default:
                $responder =  new NullResponder(20);

                break;
        }

        return $this->getFullEncodedResponder($responder);
    }

    /**
     * Used to prepare a push message to the client. After getting the return value from this, write it to the connection.
     *
     * @param array $messageArray
     * @param int $statusCode
     * @param $metaBlock
     * @param null $dataBlock
     * @return bool|string
     */
    public function prepareMessageResponder(array $messageArray, $statusCode = 1, $metaBlock = null, $dataBlock = null)
    {
        $responder = new MessageResponder($statusCode, $messageArray, $metaBlock, $dataBlock);

        return $this->getFullEncodedResponder($responder);
    }

    /**
     * called when an exception occurs in the Gateway, and there is
     * only a raw AuditBlock to deal with
     *
     * also called by buildResponder when the Bridge or Services
     * has a Response with an Exceptional AuditBlock to deal with
     *
     * @param AuditBlock $ab
     * @param $meta
     * @return string
     */
    public function handleException($ab, $meta = null)
    {
        $ex = $ab->getException();
        if (!is_null($meta)) {
            $responder = new NullResponder($ex, $meta);
        } else {
            $responder = new NullResponder($ex);
        }

        return $this->getFullEncodedResponder($responder);
    }

    //////////////////////////////////////////////////
    // PRIVATE HELPER FUNCTIONS
    //////////////////////////////////////////////////

    /**
     * Helper function to handle parsing an array into a responder.
     *
     * @param $responseData
     * @return DataResponder|NullResponder
     */
    private function packageResponder($responseData)
    {
        if (!array_key_exists('status', $responseData) && !array_key_exists('data', $responseData)) {
            $statusCode = 2;
        } else {
            $statusCode = $responseData['status']['code'];
            unset($responseData['status']);
        }

        if (!count($responseData)) {
            $responder = new NullResponder($statusCode);
        } else {
            if (array_key_exists('data', $responseData) && count($responseData['data'])) {
                if (array_key_exists('meta', $responseData)) {
                    $responder = new DataResponder($responseData['data'], $statusCode, $responseData['meta']);
                } else {
                    $responder = new DataResponder($responseData['data'], $statusCode);
                }
            } elseif (array_key_exists('meta', $responseData) && count($responseData['meta'])) {
                $responder = new NullResponder($statusCode, $responseData['meta']);
            }
        }

        return $responder;
    }

    /**
     * Determine what is inside of the Response Object that was received and send it off for responder packaging.
     *
     * @param Response $responseData
     * @return DataResponder|NullResponder|string
     */
    private function handleResponseObject(Response $responseData)
    {

            if ($responseData->blockExists('data') && count($responseData->getDataBlock()->getDataArray())) {
                if (!$responseData->blockExists('status')) {
                    $responseData->injectBlock(new StatusBlock(1));
                }
                if (array_key_exists('data', $responseData->getDataBlock()->getDataArray())) {
                    $responseData->getDataBlock()->setDataArray($responseData->getDataBlock()->getDataArray()['data']);
                }
                $responder = $this->packageResponder($responseData->objectToArray());
            } else {
                if (!$responseData->blockExists('status')) {
                    $responseData->injectBlock(new StatusBlock(2));
                }
                $responder =  $this->packageResponder($responseData->objectToArray());
            }


        return $responder;
    }

    /**
     * Encode and build full export using the set factory
     *
     * @param $responder
     * @return bool|string
     */
    private function getFullEncodedResponder($responder)
    {
        if (!$responder instanceof BasicResponder) {
            return false;
        }

        return $this->framer->encode($this->factory->buildFullExport($responder->getFull()));
    }
}
