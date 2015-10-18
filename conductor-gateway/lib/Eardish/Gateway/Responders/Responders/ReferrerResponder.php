<?php
namespace Eardish\Gateway\Responders\Responders;

use Eardish\Gateway\Responders\Core\BasicResponder;
use Eardish\Gateway\DataObjects\DataObjects\ReferrerBlock;
use Eardish\Gateway\DataObjects\DataObjects\StatusCode;

class ReferrerResponder extends BasicResponder
{
    /**
     * @param integer $statusCode
     */
    public function __construct($statusCode, $arr)
    {
        // new statuscode
        $status = new StatusCode();
        $status->setCode($statusCode);
        // insert statuscode into data array
        $this->data['status'] = $status;
        // new referrer block
        $referrer = new ReferrerBlock();
        $referrer->setReferrer($arr);
        // insert referrer into data array
        $this->data['referrer'] = $referrer;
        // Pull the glued array using getFull()
    }
}
