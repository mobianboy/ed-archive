<?php

namespace Eardish\Gateway\Formats\Factories;

use Eardish\Gateway\Formats\Core\BasicFormatFactory;

class XMLFactory extends BasicFormatFactory
{
    protected $rootName = 'root';
    protected $xmlTag = '';
    protected $openTag = '';
    protected $closeTag = '';
    protected $headers = array();
    protected $concat = '';
    const IND = '    ';

    public function __construct($headers = array())
    {
        parent::__construct($headers);
        $this->xmlTag = '<?xml version=\'1.0\' encoding=\'UTF-8\'?>'.PHP_EOL;
        $this->openTag .= '<?xml version=\'1.0\' encoding=\'UTF-8\'?>'.PHP_EOL.'<'.$this->rootName.'>'.PHP_EOL;
        $this->closeTag .= '</'.$this->rootName.'>';
    }

    public function buildFullExport($compiledArray)
    {
        $export = $this->openTag();
        $export .= $this->headers().PHP_EOL;
        $export .= $this->traverse($compiledArray, self::IND);
        $export .= $this->closeTag();

        return $export;
    }

    public function buildPartialExport($rootElement, $dataArray)
    {
        $response = '';
        $response .= $this->xmlTag;
        $response .= '<'.$rootElement.'>'.PHP_EOL;
        $response .= $this->traverse($dataArray, self::IND);
        $response .= '</'.$rootElement.'>';

        return $response;
    }

    /**
     * @param string $indents
     */
    public function traverse($dataArray, $indents)
    {
        $response = '';
        $indent = $indents;
        foreach ($dataArray as $key => $val) {
            if (is_array($val)) {
                $response .= $indent.'<'.$key.'>'.PHP_EOL;
                $response .= $this->traverse($val, $indent.self::IND);
                $response .= $indent.'</'.$key.'>'.PHP_EOL;
            } else {
                $response .= $indent.'<'.$key.'>'.$val.'</'.$key.'>'.PHP_EOL;
            }
        }

        return $response;
    }

    public function buildSingleExport($name, $value)
    {
        return '<'.$name.'>'.$value.'</'.$name.'>';
    }

    public function headers()
    {
        $indent = "";
        if (count($this->headers) && is_array($this->headers)) {
            $indent .= self::IND;
            $headers = $indent.'<headers>'.PHP_EOL;
            $headers .= $this->traverse($this->headers, $indent.self::IND);
            $headers .= $indent.'</headers>';
        } elseif (!empty($this->headers)) {
            $headers = self::IND.'<headers>'.$this->headers.'</headers>';
        } else {
            $headers = '';
        }

        return $headers;
    }
}
