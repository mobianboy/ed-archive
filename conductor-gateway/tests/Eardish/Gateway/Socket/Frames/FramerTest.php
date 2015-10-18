<?php
class FramerTest extends \PHPUnit_Framework_TestCase
{
    const FRAME_CONTINUE = 0x0;
    const FRAME_TEXT = 0x1;
    const FRAME_BINARY = 0x2;
    const FRAME_CLOSE = 0x8;
    const FRAME_PING = 0x9;
    const FRAME_PONG = 0xA;


    public function testSmallDecode()
    {
        $payload = "hello newman";

        $mask = pack("N", rand(0, pow(255, 4) - 1));

        // make a bitmask pad
        $maskpad = str_repeat($mask, ceil(1.0 * strlen($payload) / strlen($mask)));

        $maskedText = $payload ^ substr($maskpad, 0, strlen($payload));

        // make this a text frame with a FIN bit set
        $b1 = 0b10000000;
        $b1 += self::FRAME_TEXT;
        $b1 = chr($b1);
        $enc = $b1;

        $b2 = 0b10000000;               // bitmask flipped
        $b2 += strlen($payload);
        $b2 = chr($b2);
        $enc .= $b2;

        // add bitmask
        $enc .= $mask;

        // add payload
        $enc .= $maskedText;

        $framer = new \Eardish\Gateway\Socket\Frames\Framer();

        $text = $framer->decode($enc);

        $this->assertTrue($framer->isFinBitSet());
        $this->assertTrue($framer->isTextFrame());
        $this->assertFalse($framer->isBinaryFrame());
        $this->assertFalse($framer->isClose());

        $this->assertEquals($payload, $text);

    }

    public function testMedium200Decode()
    {
        $payload = "hello newman, you complete and total miscreant bastard, typifying the usual attitude problem that is often found amongst long-time, tenured, union-joining, government loving, self-serving, bureaucratic sycophants.";

        $mask = pack("N", rand(0, pow(255, 4) - 1));

        // make a bitmask pad
        $maskpad = str_repeat($mask, ceil(1.0 * strlen($payload) / strlen($mask)));

        $maskedText = $payload ^ substr($maskpad, 0, strlen($payload));

        // make this a text frame with a FIN bit set
        $b1 = 0b10000000;
        $b1 += self::FRAME_TEXT;
        $b1 = chr($b1);
        $enc = $b1;

        $b2 = 0b10000000;               // bitmask flipped
        $b2 += 126;                     // size for med payloads
        $b2 = chr($b2);
        $enc .= $b2;

        $enc .= pack("n", strlen($payload));

        // add bitmask
        $enc .= $mask;

        // add payload
        $enc .= $maskedText;

        $framer = new \Eardish\Gateway\Socket\Frames\Framer();

        $text = $framer->decode($enc);

        $this->assertTrue($framer->isFinBitSet());
        $this->assertTrue($framer->isTextFrame());
        $this->assertFalse($framer->isBinaryFrame());
        $this->assertFalse($framer->isClose());

        $this->assertEquals($payload, $text);
    }

    public function testMedium60kDecode()
    {

        $payload = "";
        for ($x = 0; $x < 60000; $x++) {
            $payload .= chr(rand(65, 90));
        }

        $mask = pack("N", rand(0, pow(255, 4) - 1));

        // make a bitmask pad
        $maskpad = str_repeat($mask, ceil(1.0 * strlen($payload) / strlen($mask)));

        $maskedText = $payload ^ substr($maskpad, 0, strlen($payload));

        // make this a text frame with a FIN bit set
        $b1 = 0b10000000;
        $b1 += self::FRAME_TEXT;
        $b1 = chr($b1);
        $enc = $b1;

        $b2 = 0b10000000;               // bitmask flipped
        $b2 += 126;                     // size for med payloads
        $b2 = chr($b2);
        $enc .= $b2;

        $enc .= pack("n", strlen($payload));

        // add bitmask
        $enc .= $mask;

        // add payload
        $enc .= $maskedText;

        $framer = new \Eardish\Gateway\Socket\Frames\Framer();

        $text = $framer->decode($enc);

        $this->assertTrue($framer->isFinBitSet());
        $this->assertTrue($framer->isTextFrame());
        $this->assertFalse($framer->isBinaryFrame());
        $this->assertFalse($framer->isClose());

        $this->assertEquals($payload, $text);
    }


    public function testLarge100kDecode()
    {

        $payload = "";
        for ($x = 0; $x < 100000; $x++) {
            $payload .= chr(rand(65, 90));
        }

        $mask = pack("N", rand(0, pow(255, 4) - 1));

        // make a bitmask pad
        $maskpad = str_repeat($mask, ceil(1.0 * strlen($payload) / strlen($mask)));

        $maskedText = $payload ^ substr($maskpad, 0, strlen($payload));

        // make this a text frame with a FIN bit set
        $b1 = 0b10000000;
        $b1 += self::FRAME_TEXT;
        $b1 = chr($b1);
        $enc = $b1;

        $b2 = 0b10000000;               // bitmask flipped
        $b2 += 127;                     // size for large payloads
        $b2 = chr($b2);
        $enc .= $b2;

        ////// REWORK THIS FOR A 64 BIT PAYLOAD LENGTH
        $enc .= pack("N",0);
        $enc .= pack("N", strlen($payload));

        // add bitmask
        $enc .= $mask;

        // add payload
        $enc .= $maskedText;

        $framer = new \Eardish\Gateway\Socket\Frames\Framer();

        $text = $framer->decode($enc);
       // die("text:" . strlen($text));

        $pl = strlen($payload);


        $this->assertTrue($framer->isFinBitSet());
        $this->assertTrue($framer->isTextFrame());
        $this->assertFalse($framer->isBinaryFrame());
        $this->assertFalse($framer->isClose());

     //   $this->assertEquals($pl, $framer->getPayloadLength());
     //   $this->assertEquals($text, $payload);
    }

    public function testBufferOverreadCatch()
    {

        $framer = new \Eardish\Gateway\Socket\Frames\Framer();

        $payload1 = "";
        for ($x = 0; $x < 1000; $x++) {
            $payload1 .= chr(rand(65, 90));
        }

        $mask = pack("N", rand(0, pow(255, 4) - 1));

        // make a bitmask pad
        $maskpad = str_repeat($mask, ceil(1.0 * strlen($payload1) / strlen($mask)));

        $maskedText = $payload1 ^ substr($maskpad, 0, strlen($payload1));

        // make this a text frame with a FIN bit set
        $b1 = 0b10000000;
        $b1 += self::FRAME_TEXT;
        $b1 = chr($b1);
        $enc = $b1;

        $b2 = 0b10000000;               // bitmask flipped
        $b2 += 127;                     // size for large payloads
        $b2 = chr($b2);
        $enc .= $b2;

        ////// REWORK THIS FOR A 64 BIT PAYLOAD LENGTH
        $enc .= pack("N",0);
        $enc .= pack("N", strlen($payload1));

        // add bitmask
        $enc .= $mask;

        // add payload
        $enc .= $maskedText;

        $pl1 = strlen($payload1);

        $payload2 = "";
        for ($x = 0; $x < 8000; $x++) {
            $payload2 .= chr(rand(65, 90));
        }

        $mask = pack("N", rand(0, pow(255, 4) - 1));

        // make a bitmask pad
        $maskpad = str_repeat($mask, ceil(1.0 * strlen($payload2) / strlen($mask)));

        $maskedText = $payload2 ^ substr($maskpad, 0, strlen($payload2));

        // make this a text frame with a FIN bit set
        $b1 = 0b10000000;
        $b1 += self::FRAME_TEXT;
        $b1 = chr($b1);
        $enc .= $b1;

        $b2 = 0b10000000;               // bitmask flipped
        $b2 += 127;                     // size for large payloads
        $b2 = chr($b2);
        $enc .= $b2;

        ////// REWORK THIS FOR A 64 BIT PAYLOAD LENGTH
        $enc .= pack("N",0);
        $enc .= pack("N", strlen($payload2));

        // break payload apart at an inconvenient place in the stream

        // two frames - one decode call - lets see what happens
        $text1 = $framer->decode($enc);

        $bufferRemaining = $framer->getBufferOverread();

        // add bitmask
        $enc = $mask;

        // add payload
        $enc .= $maskedText;
        $pl2 = strlen($payload2);
        $framer = new \Eardish\Gateway\Socket\Frames\Framer();

        $framer->setBufferOverread($bufferRemaining);


        // die("text:" . strlen($text));

        $text2 = $framer->decode($enc);
    //    $this->assertEquals($payload1, $text1);
     //   $this->assertEquals($payload2, $text2);
        $this->assertNotNull($text1);
        $this->assertNotNull($text2);
        $this->assertNotEquals($text1, $text2);
        $this->assertNotEquals($payload1, $payload2);




    }





    public function testSmallEncode()
    {
        $payload = "";
        for ($x = 0; $x < 100; $x++) {
            $payload .= chr(rand(65, 90));
        }
        $pl = strlen($payload);

        $framer = new \Eardish\Gateway\Socket\Frames\Framer();

        $enc = $framer->encode($payload);


        //// BEGIN - read the header (16 bits)
        list($b1, $b2) = substr($enc, 0, 2);

        $b1 = ord($b1);
        $b2 = ord($b2);


        // $this->finBit = ($b1 & self::HIBIT8_MASK);

        //  $finBit = $this->isBitSet($b1,7);
        $finBit = (bool)($b1 & 0b10000000);

        $rsv1 = (bool)(($b1 >> 6) & 1); // self::HIBIT8_MASK;
        $rsv2 = (bool)(($b1 >> 5) & 1); // self::HIBIT8_MASK;
        $rsv3 = (bool)(($b1 >> 4) & 1); // self::HIBIT8_MASK;

        $continueFrame = $b1 & self::FRAME_CONTINUE;
        $textFrame = (bool)($b1 & self::FRAME_TEXT);
        $binaryFrame = $b1 & self::FRAME_BINARY;
        $close = $b1 & self::FRAME_CLOSE;
        $ping = $b1 & self::FRAME_PING;
        $pong = $b1 & self::FRAME_PONG;


        // mask bit - ALWAYS set coming from the client
        $maskBit = (bool)($b2 & 0b10000000);

        // payload length$this->bitmask
        $pLen = $b2 & ~128;

        $data = substr($enc, 2, $pLen);


        // $this->assertEquals($rsv1,0);
        $this->assertFalse($rsv1);
        $this->assertFalse($rsv2);
        $this->assertFalse($rsv3);
        $this->assertFalse($maskBit);
        $this->assertEquals($pLen, $pl);
        $this->assertTrue($finBit);
        $this->assertTrue($textFrame);

        $this->assertEquals($payload, $data);


        /*
                 *
                 *
                } elseif ($this->pLen = 126) {    // convert next two bytes to payload length

                    $bytes = unpack("nfirst", $this->read(2));
                    $this->payloadLength = array_pop($bytes);
                    if(!($this->payloadLength))
                        return null;

                } elseif ($this->pLen = 127) {    // convert next eight bytes to payload length
                    $this->payLoadLength = $this->read(8);
                    list(,$high,$low) = unpack("N2", $this->payLoadLength);   // N2 = two, 32 bit unsigned ints
                    $this->payloadLength = ($low + ($high * 0x0100000000));
                    die($this->payloadLength);
               }
        */


    }

    public function testMedEncode5k()
    {
        $payload = "";
        for ($x = 0; $x < 5000; $x++) {
            $payload .= chr(rand(68, 90));
        }
        $pl = strlen($payload);

        $framer = new \Eardish\Gateway\Socket\Frames\Framer();

        $enc = $framer->encode($payload);
    //    die("payload $payload\n enc $enc");

   //     $encLen = strlen($enc);


        //// BEGIN - read the header (16 bits)
        list($b1, $b2) = substr($enc, 0, 2);

        $b1 = ord($b1);
        $b2 = ord($b2);


        // $this->finBit = ($b1 & self::HIBIT8_MASK);

        //  $finBit = $this->isBitSet($b1,7);
        $finBit = (bool)($b1 & 0b10000000);

        $rsv1 = (bool)(($b1 >> 6) & 1); // self::HIBIT8_MASK;
        $rsv2 = (bool)(($b1 >> 5) & 1); // self::HIBIT8_MASK;
        $rsv3 = (bool)(($b1 >> 4) & 1); // self::HIBIT8_MASK;

        $continueFrame = $b1 & self::FRAME_CONTINUE;
        $textFrame = (bool)($b1 & self::FRAME_TEXT);
        $binaryFrame = $b1 & self::FRAME_BINARY;
        $close = $b1 & self::FRAME_CLOSE;
        $ping = $b1 & self::FRAME_PING;
        $pong = $b1 & self::FRAME_PONG;


        // mask bit - ALWAYS set coming from the client
        $maskBit = (bool)($b2 & 0b10000000);

        // payload length$this->bitmask
        $pLen = $b2 & ~128;

        $bytes = unpack("nfirst", substr($enc, 2, 2));
        $payloadLength = array_pop($bytes);

        $data = substr($enc,4);


        // $this->assertEquals($rsv1,0);
        $this->assertFalse($rsv1);
        $this->assertFalse($rsv2);
        $this->assertFalse($rsv3);
        $this->assertFalse($maskBit);
        $this->assertEquals($pLen, 126);
     //   $this->assertEquals($encLen,$pLen + 4);
        $this->assertEquals($pl, $payloadLength);
        $this->assertTrue($finBit);
        $this->assertTrue($textFrame);

        $this->assertEquals($payload, $data);


    }


    public function testLargeEncode100k()
    {
        $payload = "";
        for ($x = 0; $x < 100000; $x++) {
            $payload .= chr(rand(68, 90));
        }
        $pl = strlen($payload);

        $framer = new \Eardish\Gateway\Socket\Frames\Framer();

        $enc = $framer->encode($payload);
        //    die("payload $payload\n enc $enc");

        //     $encLen = strlen($enc);


        //// BEGIN - read the header (16 bits)
        list($b1, $b2) = substr($enc, 0, 2);

        $b1 = ord($b1);
        $b2 = ord($b2);

        // $this->finBit = ($b1 & self::HIBIT8_MASK);

        //  $finBit = $this->isBitSet($b1,7);
        $finBit = (bool)($b1 & 0b10000000);

        $rsv1 = (bool)(($b1 >> 6) & 1); // self::HIBIT8_MASK;
        $rsv2 = (bool)(($b1 >> 5) & 1); // self::HIBIT8_MASK;
        $rsv3 = (bool)(($b1 >> 4) & 1); // self::HIBIT8_MASK;

        $continueFrame = $b1 & self::FRAME_CONTINUE;
        $textFrame = (bool)($b1 & self::FRAME_TEXT);
        $binaryFrame = $b1 & self::FRAME_BINARY;
        $close = $b1 & self::FRAME_CLOSE;
        $ping = $b1 & self::FRAME_PING;
        $pong = $b1 & self::FRAME_PONG;


        // mask bit - ALWAYS set coming from the client
        $maskBit = (bool)($b2 & 0b10000000);

        // payload length$this->bitmask
        $pLen = $b2 & ~128;

        $by = unpack("N*", substr($enc, 2, 8));

        // $by1 = $by[1] * 0x10000000;
        $payloadLength = $by[2];

        $data = substr($enc,10);


        // $this->assertEquals($rsv1,0);
        $this->assertFalse($rsv1);
        $this->assertFalse($rsv2);
        $this->assertFalse($rsv3);
        $this->assertFalse($maskBit);
        $this->assertEquals($pLen, 127);
        //   $this->assertEquals($encLen,$pLen + 4);
        $this->assertEquals($pl, $payloadLength);
        $this->assertTrue($finBit);
        $this->assertTrue($textFrame);

        $this->assertEquals($payload, $data);


    }


}