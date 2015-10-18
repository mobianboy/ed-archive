<?php
namespace Eardish\DataObjects\Blocks;

class DataBlockTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DataBlock
     */
    protected $object;

    public function setUp()
    {
        $this->object = new DataBlock(array("data" => array("bio" => "bioData")));
    }

    public function testGetData()
    {
        $output = $this->object->getDataArray();

        $this->assertEquals(
            array("data" => array("bio" => "bioData")),
            $output
        );
    }

    public function testSetComponent()
    {
        $this->object->setDataArray(array("data" => "new data"));

        $this->assertEquals(
            $this->object->getDataArray(),
            ["data" => "new data"]
        );
    }

}
