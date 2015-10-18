<?php
namespace Eardish\EmailService\Core\Config;

class JSONLoader
{
    /**
     * @param string $file
     * @return mixed
     */
    public function loadJSONConfig($file)
    {
        if (file_exists($file)) {
            $jsonConfig = file_get_contents($file);
            return json_decode($jsonConfig, true);
        } else {
            return array();
        }
    }
}