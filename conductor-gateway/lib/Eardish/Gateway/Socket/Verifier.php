<?php
namespace Eardish\Gateway\Socket;

class Verifier
{
    public $headers;

    public function __construct($data)
    {
        $headers = explode("\n", $data);
        // Shift off protocol
        array_shift($headers);
        foreach ($headers as $value) {
            if (strpos($value, ':') === false) {
                continue;
            }
            list($key, $val) = explode(':', $value, 2);
            $this->headers[trim($key)] = trim($val);
        }
    }

    public function isValidUpgrade()
    {
        if ((strpos($this->headers['Connection'], 'Upgrade') !== false) && ($this->headers['Upgrade'] == 'websocket') && (isset($this->headers['Sec-WebSocket-Key']))) {
            return true;
        }

        return false;
    }

    public function getWSKey()
    {
        return $this->headers['Sec-WebSocket-Key'];
    }
}
