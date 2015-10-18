<?php
namespace Eardish\DataObjects\Blocks;

class DataBlock
{
    /**
     * @var array
     */
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getDataArray()
    {
        return $this->data;
    }

    public function setDataArray(array $data)
    {
        $this->data = $data;
    }
}