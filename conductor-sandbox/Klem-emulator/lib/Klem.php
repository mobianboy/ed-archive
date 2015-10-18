<?php

class Klem
{
    /**
     * @var string
     */
    protected $name;
    /**
     * @var string
     */
    protected $desc;
    /**
     * @var string
     */


    function __construct($text)
    {
        $this->testConfigFile = $text;

        // use JSONMapper to convert JSON to an array
        // and load the proper objects where needed
    }



}