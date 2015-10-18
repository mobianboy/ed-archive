<?php
namespace Eardish\Gateway\Socket\Frames;

class Framer
{
    /**
     * the data payload after applying bitmask and conversion
     * @var string
     */
    protected $clearText;

    /**
     * websockets flags
     */
    protected $finBit;
    protected $rsv1;
    protected $rsv2;
    protected $rsv3;
    // protected $finalFrame;
    protected $continueFrame;
    protected $textFrame;
    protected $binaryFrame;
    protected $close;
    protected $ping;
    protected $pong;


    /**
     * maintain request state
     */
    protected $requestHasPing;
    protected $requestHasPong;
    protected $requestBinaryframe;
    protected $requestTextframe;
    protected $requestCloseframe;

    /**
     * payload length stuff
     * */
    protected $pLen;
    protected $payloadLength;

    /**
     * data stuff
     */
    //   protected $data;
    protected $bitmask;
    protected $maskBit;
    protected $buffer;          // buffer (array)
    protected $rawData;         // raw data string from client

    protected $bufferRemainder;  // must be returned after an overread


    /**
     * application state/status flags
     */
    protected $headerSet;
    protected $payloadSizeSet;
    protected $bitMaskSet;
    protected $partialFrameReadStatus;
    protected $finished;
    protected $error;
    protected $errorMsg;


    /**
     * websockets opcode stuff
     */
    const FRAME_CONTINUE =   0x0;
    const FRAME_TEXT     =   0x1;
    const FRAME_BINARY   =   0x2;
    const FRAME_CLOSE    =   0x8;
    const FRAME_PING     =   0x9;
    const FRAME_PONG     =   0xA;

    const HIBIT8_MASK    =   0b10000000;


    function __construct()
    {

        $this->rawData          = "";
        $this->clearText        = "";
        $this->payloadLength    = 0;
        $this->pLen             = 0;
        $this->binaryFrame      = false;
        $this->ping             = false;
        $this->pong             = false;
        $this->finished         = false;
        $this->rsv1             = false;
        $this->rsv2             = false;
        $this->rsv3             = false;
        $this->finBit           = false;
        $this->maskBit          = false;

        //  $this->finalFrame       = false;

        // state
        $this->headerSet        = false;
        $this->payloadSizeSet   = false;
        $this->bitMaskSet       = false;

        $this->requestHasPing       = false;
        $this->requestHasPong       = false;
        $this->requestBinaryframe   = false;
        $this->requestTextframe     = false;
        $this->requestCloseframe    = false;
        $this->error                = 1000;             // RFC for okay
        $this->partialFrameReadStatus     = false;
        $this->errorMsg             = "";

        $this->bufferRemainder            = "";
    }

    //////////////////////////////////////////////////////////
    // decoder methods
    ////////////////////////////////////////////////

    /**
     * @param string
     * @return string
     */
    function decode(&$data)
    {
        // start data from scratch
  //      $this->reset();
        // append data to the data queue
        $this->append($data);
        // read header (16 bits)
        if(!($this->headerSet)) {
            if($this->decodeHeader() == null) {
                return null;
            }
        }

        // any flags set that shouldn't be?
        if(!($this->isFrameValid()))
        {
            $this->error = 1002;
            $this->errorMsg = "endpoint terminating connection due to protocol error - invalid bits set";
            return false;
        }

        // read payload size (0-64 bits read)
        if(!($this->payloadSizeSet)) {
            if($this->decodePayloadSize() == null) {
                $this->error = 1002;
                $this->errorMsg = "endpoint terminating connection due to protocol error - payload size";
                return null;
            }
        }

        // read mask (32 bits)

        // check to see if the frame is valid
        if(!($this->bitMaskSet)) {
            if($this->decodeBitMask() == null) {
                $this->error = 1002;
                $this->errorMsg = "endpoint terminating connection due to protocol error - bitmask not set";
                return null;
            }
        }

        // read payload data (payloadSize bits)
        if(($this->decodeData() == null)) {
            return null;
        }

        // check for errors or close frames to handle in response frame
        if(($this->error!=1000)||($this->close)) {
            return $this->packErrorFrame();
        }

        return $this->clearText;
    }

    /**
     * decodes the websocket header
     * @return boolean
     */
    protected function decodeHeader()
    {
        //// BEGIN - read the header (16 bits)
        if(!(list($b1, $b2) = $this->read(2))) {
            return null;            // not enough data
        }

        $b1 = ord($b1);
        $b2 = ord($b2);

        // $this->finBit = ($b1 & self::HIBIT8_MASK);

        $this->finBit = $this->isBitSet($b1,7);
        // WE ARE READING RFC6455 FROM LEFT TO RIGHT - BITSHIFT LEFT


        // RSV bits - reserved bits MUST always be false
        //  $this->rsv1 = ($b1 << 1) & self::HIBIT8_MASK;
        //  $this->rsv2 = ($b1 << 2) & self::HIBIT8_MASK;
        //  $this->rsv3 = ($b1 << 3) & self::HIBIT8_MASK;

        $this->rsv1 = $this->isBitSet($b1,6);
        $this->rsv2 = $this->isBitSet($b1,5);
        $this->rsv3 = $this->isBitSet($b1,4);

        // opcode stuff

        $this->continueFrame    = $b1 & self::FRAME_CONTINUE;
        $this->textFrame    = $b1 & self::FRAME_TEXT;
        $this->binaryFrame  = $b1 & self::FRAME_BINARY;
        $this->close        = $b1 & self::FRAME_CLOSE;
        $this->ping         = $b1 & self::FRAME_PING;
        $this->pong         = $b1 & self::FRAME_PONG;


        // mask bit - ALWAYS set coming from the client
        $this->maskBit = $b2 & self::HIBIT8_MASK;

        // payload length$this->bitmask
        $this->pLen  = $b2 & ~128;

        return $this->headerSet = true;
    }

    /**
     * decodes payload size
     * @return boolean
     */
    protected function decodePayloadSize()
    {

        if($this->pLen < 126) {           // already have it, return
            $this->payloadLength = $this->pLen;

        } elseif ($this->pLen == 126) {    // convert next two bytes to payload length

            $bytes = unpack("nfirst", $this->read(2));
            $this->payloadLength = array_pop($bytes);
            if(!($this->payloadLength))
                return null;

        } elseif ($this->pLen == 127) {    // convert next eight bytes to payload length

            $bytes = $this->read(8);
      //      list($high,$low)
            $by = unpack("N2", $bytes);   // N2 = two, 32 bit unsigned ints
            // echo $this->pLen;
            // die("high: $by[1] - low: $by[2]");
            $this->payloadLength = ($by[2] + ($by[1] * 0x0100000000));
          //  die($this->payloadLength);

        } else {
            $this->error = 1002;
            $this->errorMsg = "protocol error: payload length not readable (pLen read as $this->pLen)";
        }

        /** UNCOMMENT THIS FOR RFC MESSAGE SIZE COMPLIANCE ON COMMAND FRAMES
        // per RFC -- control frames MUST have a payload size of 125 or less
        if((($this->ping)||($this->pong)||($this->close))&&($this->payloadLength>125)) {
            $this->error = 1002;
            $this->errorMsg = "protocol error:  payload length for control frames must be 125 bytes or less";
            return false;
        }
        */
        $this->payloadSizeSet = true;
        return true;
    }

    /**
     * decode bitmask
     * @return boolean
     */
    protected function decodeBitMask()
    {
        if((bool)$this->maskBit) {
            if(!($this->bitmask = $this->read(4))) {
                return null;        // not enough data
            }
        }
        $this->bitMaskSet = true;   // not an indicator if the bitMask was set or not
        return true;
    }

    /**
     * decode and unpack payload
     * @return mixed
     */

    protected function decodeData()
    {

        // if a text frame, make sure the data is UTF8 - if not, fail the websocket
        if($this->textFrame) {
            if(!(mb_check_encoding($this->clearText, "UTF-8"))) {
                $this->error = 1007;
                $this->errorMsg = "endpoint terminating connection - textframe data expected to be UTF-8";
                return false;
            }
        }

        // apply the bitmask to the data, and if successful, flip the finished flag so the
        // front controller can clean up - will only return null if there is not enough data
        // to do the operation and it needs more
        if($this->clearText = $this->applyBitmask()) {
            $this->finished = true;
            return $this->clearText;
        } else {
            return null;
        }
    }

    ///////////////////////////////////////////////////////////////////
    // encoder methods
    ///////////////////////////////////////////////////////

    /**
     * @param string
     * @return string
     */
    function encode($data)
    {
        $this->reset();
        // TODO -- FLAGS ARE MOCKED -- MAKE THEM REAL

        // check to see if we are encoding a close frame resulting from an error
        if($this->error != 1000) {
            $this->close = true;
        }


        // set required flags for the frame header

        // == byte one -----------------------------------------

        // fin
        $this->finBit = true;               // always true unless the message is fragmented

        // rsv1,rsv2,rsv3 (always false)
        $this->rsv1 = false;
        $this->rsv2 = false;
        $this->rsv3 = false;

        // set frame type (opcode)
        if(!($this->close)||($this->error!=1000)) {
            $this->continueFrame = false;
            $this->textFrame = true;         // FIX -- needs to be determined by payload data type
            $this->binaryFrame = false;
            $this->ping = false;
            $this->pong = false;
        }

        ////// FIX
        // hard code in the opcode for textframe -- FIX THIS -- NEEDS TO BE TOLD WHAT TYPE THE PAYLOAD DATA IS
 //       $this->requestTextframe = true;
        $op = "";
//
//        // talk the same frame type

        if($this->textFrame) {
            $op += self::FRAME_TEXT;
        }

        if($this->binaryFrame) {
            $op += self::FRAME_BINARY;
        }

        if($this->close) {
            $op += self::FRAME_CLOSE;
        }

        $b1 = $op;
        // make the first 8 bits
        $b1 += $this->finBit * 128 + $this->rsv1 * 64 + $this->rsv2 * 32 + $this->rsv3 * 16;

        $enc = chr($b1);

        // == byte two ----------------------------------------

        // set bitmask flag (for server, keep false)
        $this->maskBit          = false;

        // get payload length
        $this->payloadLength = strlen($data);

        if($this->payloadLength <= 125) {
            $b2 = $this->payloadLength;

            $b2 += ($this->maskBit * 128);
            $enc .= chr($b2);
        } else if(($this->payloadLength > 125 ) && ($this->payloadLength < (256*256-1))) {
            $b2 = 126;
            $b2 += ($this->maskBit * 128);
            $enc .= chr($b2) . pack("n", $this->payloadLength);
        } else {
            $b2 = 127;
            $b2 += ($this->maskBit * 128);
            $enc .= chr($b2);
          //  $bin = decbin($this->payloadLength);

            // NOTE: PACKING THE FIRST FOUR BYTES WITH 0 LIMITS US TO 32 BIT payload LEN
            $enc .= pack("N",0);
            $enc .= pack("N",$this->payloadLength);
        }

        // finally, add the data
        // TODO -- ADD XOR'ing the bitmask against the data if server bitmasking needed
        $key = 0;
        if ($this->payloadLength) {
            $enc .= ($this->maskBit == 1) ? $this->applyBitmask($this->$data, $key) : $data;
        }


        // and return the binary string
        return $enc;
    }

    //////////////////////////////////////////////////////////////////
    // utility methods
    /////////////////////////////////////////////////////

    /**
     * makes sure we don't have a mangled packet of data
     *
     * check each of the places the RFC requires an auto CLOSE frame and status code
     * to be sent back to the initiating client
     *
     * the conditions in which to 'FAIL THE WEBSOCKET' in RFC6455
     *
     * bad op codes
     *
     * RSV1, RSV2, RSV3:  must all be zero or FAIL (no extensions here)
     * unknown opcode - RFC sec 5.1
     *
     * Client doesn't have the bitmask flag turned up (RFC sec 5.3)
     *

     */
    function isFrameValid()
    {
        // check the bits
        if (($this->rsv1)||($this->rsv2)||($this->rsv3)) {
            // TODO - write error to log
            return false;
        }
        // check the op codes
        if (!(($this->textFrame)||($this->binaryFrame)||($this->close)||($this->ping)||($this->pong))) {
            // TODO - write error to log
            return false;
        }
        return true;
    }


    /**
     * returns the error code of the decode
     */
    function error()
    {
        return $this->error;
    }

    function errorMessage()
    {
        return $this->errorMsg;
    }


    /**
     * packErrorFrame packs an error frame to return where there
     * is a problem - uses current status information
     *
     */
    function packErrorFrame()
    {
        $enc = pack("S",$this->error);
        $enc .= $this->errorMsg;
        return $enc;
    }


    /**
     * */


    /**
     * sets vars to defaults
     */
    function reset()
    {
        // check the request flags if they are set
        if($this->ping) {
            $this->requestHasPing       = true;
        }
        if($this->pong) {
            $this->requestHasPong       = true;
        }
        if($this->textFrame) {
            $this->requestTextframe     = true;
        }
        if($this->binaryFrame) {
            $this->requestBinaryframe   = true;
        }



    }

    /**
     * append data to the incoming buffer to be decoded
     * @param $data
     */
    protected function append($data)
    {
        //   echo "\n\nDATA>>> $data \n\n"
        //   fwrite($this->dataStream, $data);
        //   rewind($this->dataStream);
        //   $nb = strlen($data);
        //   for($i = 0; $i < $nb ;$i++) {
        //       array_push($this->buffer, ord(substr($data,$i,1)));
        $this->rawData .= $data;
    }

    /**
     * reads $size bytes from the data buffer
     * removes the read data from the buffer
     *
     * @param integer
     * @return string of $size bytes
     * @return string of $size bytes
     *
     * TODO -- NEEDS TO RETURN NULL IF $size BYTES AREN'T AVAILABLE YET
     *
     */
    protected function read($size)
    {
        if($this->partialFrameReadStatus) {
            $this->partialFrameReadStatus = false;
        }

        // if it doesn't have enough to do a full read
        // leaves the buffer alone and returns null
        if(strlen($this->rawData) < $size) {
            $this->partialFrameReadStatus = true;
            return null;
        }

        // full frame read
        $val = substr($this->rawData,0,$size);
        $this->rawData = substr($this->rawData,$size);

        return $val;


        // return fread($this->dataStream, $size);
        //if(array_count_values($this->buffer) < $size) {
        //    return null;
        // }
        //$reta = array();
        //for($count=0;$count<$size;$count++) {
        //    array_push($reta, array_shift($this->buffer));
        // }
        // return $reta;
    }


    /**
     * after a request has completed, we must check here to see if
     * we overread the buffer into the next request.  it happens.
     * ALWAYS call this function after a completed read.
     */
    public function getBufferOverread()
    {
        return $this->bufferRemainder;
    }

    public function bufferOverreadBy()
    {
        return strlen($this->bufferRemainder);
    }


    /**
     * when creating a new instance of the
     * @param $buf
     */
    public function setBufferOverread($buf)
    {
        if(strlen($buf)>0) {
            $this->rawData = $buf;
        }
    }



    protected function IsBitSet($byte, $pos)
    {
        return ($byte & pow(2, $pos)) > 0 ? 1 : 0;
    }


    /**
     * creates a pseudo-random bitmask for the server in the event
     * that optional RFC server bitmasking needs to be implemented
     */
    protected function createMask()
    {
        if ($this->mask) {
            $key = pack("N", rand(0, pow(255, 4) - 1));
            return $key;
        }

        return null;
    }

    ///////////////////////////////////////////////////////////////////
    // helpers and accessors
    ///////////////////////////////////////


    /**
     * @param
     */
    protected function applyBitmask()
    {

        // get the length of payload data
        $rawdata = $this->read($this->payloadLength);

        // check to see if there was enough data to do the read, if not go back for more data
        if(!($rawdata)) {
            return null;
        }

        // make a bitmask pad
        $maskpad = str_repeat($this->bitmask, ceil(1.0*strlen($rawdata) / strlen($this->bitmask)));

        return $rawdata ^ substr($maskpad, 0, strlen($rawdata));

    }

    /**
     * returns TRUE if this is a continue frame
     * since continue frames are 0x0, if the opcode is 0x0
     * then we must invert in order to get a TRUE response
     * when asking 'is this a continueFrame?'
     *
     * @return bool
     */
    function isContinueFrame()
    {
        return (!(bool)$this->continueFrame);
    }

    /**
     * returns TRUE if a binary frame
     * @return bool
     */
    function isBinaryFrame()
    {
        return (bool)$this->binaryFrame;
    }

    /**
     * returns true if a text frame
     * @return bool
     */
    function isTextFrame()
    {
        return (bool)$this->textFrame;
    }

    /**
     * @return bool
     */
    function isClose()
    {
        return (bool)$this->close;
    }

    /**$this->data
     * @return bool
     */
    function isPing()
    {
        return (bool)$this->ping;
    }

    /**
     * @return bool
     */
    function isPong()
    {
        return (bool)$this->pong;
    }

    /**
     * @return bool
     */
    function isFinBitSet()
    {
        return (bool)$this->finBit;
    }


    function partialFrameRead()
    {
        return (bool)$this->partialFrameReadStatus;
    }

    /**
     * @return bool
     */
    function isFinished()
    {
        return $this->finished;
    }

    function getPayloadLength()
    {
        return $this->payloadLength;
    }
}