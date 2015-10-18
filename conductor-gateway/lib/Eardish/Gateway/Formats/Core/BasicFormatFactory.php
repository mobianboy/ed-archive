<?php

namespace Eardish\Gateway\Formats\Core;

use Eardish\Gateway\Interfaces\FormatFactoryInterface;

abstract class BasicFormatFactory implements FormatFactoryInterface
{
    protected $openTag;
    protected $closeTag;
    protected $headers = array();
    protected $concat;

    public function __construct($headers = array())
    {
        $this->headers = $headers;
    }

    public function buildFullExport($compiledArray)
    {
        $processed = $this->openTag();
        $processed .= $this->headers().$this->concat;
        $processed .= implode($this->concat, $compiledArray);
        $processed .= $this->closeTag();

        return $processed;
    }

    abstract public function buildPartialExport($rootElement, $dataArray);

    abstract public function buildSingleExport($name, $value);

    public function openTag()
    {
        return $this->openTag;
    }

    public function closeTag()
    {
        return $this->closeTag;
    }

    abstract public function headers();
}
