<?php
namespace Eardish\Gateway\Responders\Core;

use Eardish\Gateway\Interfaces\ResponderInterface;

abstract class BasicResponder implements ResponderInterface
{
    protected $data = array();
    protected $export = array();

    public function getBlock($block)
    {
        if (!isset($this->data[$block])) {
            throw new \InvalidArgumentException();
        }

        return $this->data[$block];
    }
    public function getFull()
    {
        foreach ($this->data as $dataobject) {
            $this->export[$dataobject->getName()] = $dataobject->getOptions();
        }

        return $this->export;
    }

    public function getData()
    {
        return $this->data;
    }
}
