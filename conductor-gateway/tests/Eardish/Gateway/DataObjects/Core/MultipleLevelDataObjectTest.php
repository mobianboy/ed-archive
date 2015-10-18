<?php

namespace Eardish\Gateway\DataObjects\Core;

class MultipleLevelDataObjectTest extends \PHPUnit_Framework_TestCase
{
    protected $object;
    
    protected function setUp()
    {
        $structure = array(
            "code" => true,
            "message" => true,
            "meta" => array(
                "date" => true,
                "author" => true
            )
            );
        
        $this->object = new MultipleLevelDataObject($structure, "status");
    }
    
    public function testStructure()
    {
        $structure = array(
            "code" => true,
            "message" => true,
            "meta" => array(
                "date" => true,
                "author" => true
            )
            );
        
        $this->assertEquals(
                $structure,
                $this->object->getStructure()
                );
    }
    
    /**/
    public function testIsOptionSettableString()
    {
        $this->assertTrue(
                $this->object->isOptionSettable("code")
                );
        
        $this->assertFalse(
                $this->object->isOptionSettable("notakey")
                );
    }
    /**/
    
    /**/
    public function testIsOptionSettablePath()
    {
        $this->assertTrue(
                $this->object->isOptionSettable("meta.date")
                );
        
        $this->assertFalse(
                $this->object->isOptionSettable("meta.notdate")
                );
        
        $this->assertFalse(
                $this->object->isOptionSettable("meta.date.third")
                );
    }
    /**/
    
    public function testSetOptionsAndExport()
    {
        $this->assertEquals(
                '',
                $this->object->getOption('meta.date')
                );
        
        $values = array(
            'code' => 10,
            'meta' => array(
                'date' => 'today'
            )
        );
        
        $this->object->setOptions($values);
        
        $this->assertEquals(
                10,
                $this->object->getOption('code')
                );
        
        $this->assertEquals(
                'today',
                $this->object->getOption('meta.date')
                );
        
        $this->assertEquals(
                '',
                $this->object->getOption('meta.author')
                );
        
        $this->assertEquals(
                $values,
                $this->object->getOptions()
                );
    }
    
    public function testRecurseArray()
    {
        $input = array(
            "test1" => array(
                "test2" => array(
                    "test3" => "val",
                    "test4" => "val"
                ),
                "test5" => "val"
            )
        );
                
        $output = array(
            "test1.test2.test3" => "val",
            "test1.test2.test4" => "val",
            "test1.test5" => "val"
            );
        
        $this->assertEquals(
                $output,
                $this->object->recurseArray($input)
                );
    }
    public function testGetName()
    {
        $this->assertEquals(
            'status',
            $this->object->getName()
        );
    }
    public function testGetOptionArrayException()
    {
        $this->setExpectedException('InvalidArgumentException');
        
        $this->object->getOption(array('code'));
    }
    
    public function testGetOptionNotSettableException()
    {
        $this->setExpectedException('InvalidArgumentException');
        
        $this->object->getOption('code.something');
    }
    
//    public function testSetOptionsException()
//    {
//        $this->setExpectedException('InvalidArgumentException');
//
//        $this->object->setOptions(array('code' => array('something' => 'val')));
//    }
}