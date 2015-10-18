<?php

namespace Eardish\Gateway\Formats\Factories;

use Eardish\Gateway\Formats\Core\BasicFormatFactory;

class JSONFactory extends BasicFormatFactory
{
    protected $openTag = "{";
    protected $closeTag = "}";
    protected $headers = array();
    protected $concat = ",";

    public function __construct($headers = array())
    {
        parent::__construct($headers);
    }

    public function buildFullExport($compiledArray)
    {
        $export = $this->openTag().PHP_EOL;
        $export .= $this->headers();
        $i = 1;
        $num = count($compiledArray);
        foreach ($compiledArray as $key => $val) {
            $export .= $this->buildPartialExport($key, $val);
            if ($i < $num) {
                $export .= $this->concat;
            }
            $export .= PHP_EOL;

            $i++;
        }
        $export .= $this->closeTag();

        return $export;
    }

    public function buildPartialExport($rootElement, $dataArray)
    {
        return "\"".$rootElement."\": ".json_encode($dataArray);
    }

    public function buildSingleExport($name, $value)
    {
        return (is_int($value)) ? "\"".$name."\": ".$value : "\"".$name."\": \"".$value."\"";
    }

    public function headers()
    {
        return (count($this->headers)) ? "\"headers\": ".json_encode($this->headers).$this->concat.PHP_EOL : "";
    }
}
