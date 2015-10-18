<?php
namespace Eardish\Gateway;

class ValidationTest extends \PHPUnit_Framework_TestCase
{
    protected $object;

    public function setUp()
    {
        $this->object = new Validation;
    }

    public function testValidate()
    {
        //string tests
        /**
         * length() default for inclusion is backwards in documentation.
         * submitted github issues, but watch for any changes.
         * actual default is $inclusive = true.
         */
        $this->assertTrue(
            $this->object->validate("stringLengthGreaterThan15", "length::15&&30")
        );
        $this->assertFalse(
            $this->object->validate("hi", "length::0||2")
        );
        $this->assertTrue(
            $this->object->validate("hi", "length::0&&2")
        );

         //number tests
        $this->assertTrue(
            $this->object->validate(12345, "int,,odd")
        );
        $this->assertFalse(
            $this->object->validate(12345, "int,,even")
        );
        $this->assertFalse(
            $this->object->validate(123.45, "int,,odd")
        );
        $this->assertTrue(
            $this->object->validate(20.6, "positive,,even")
        );
        $this->assertTrue(
            $this->object->validate(-11, "negative,,odd")
        );

        //between
        $this->assertTrue(
            $this->object->validate(37, "int,,odd,,positive,,between::10&&100")
        );
        $this->assertTrue(
            $this->object->validate(10, "int,,even,,positive,,between::10&&100")
        );
        $this->assertTrue(
            $this->object->validate(100, "int,,even,,positive,,between::10&&100")
        );
        $this->assertFalse(
            $this->object->validate(10, "int,,even,,positive,,between::10||100")
        );
        $this->assertFalse(
            $this->object->validate(100, "int,,even,,positive,,between::10||100")
        );
        $this->assertFalse(
            $this->object->validate(2, "int,,even,,positive,,between::10||100")
        );
        $this->assertTrue(
            $this->object->validate("2000/3/3", "date,,between::2000/1/1&&2001/1/1")
        );

        //email tests
        $this->assertTrue(
            $this->object->validate("test@gmail.com", "email")
        );
        $this->assertTrue(
            $this->object->validate("test.email@gmail.com", "email")
        );
        $this->assertFalse(
            $this->object->validate("test not an email@gmail**.com", "email")
        );

        //date tests
        $this->assertTrue(
            $this->object->validate("3/15/2014", "date")
        );
        $this->assertTrue(
            $this->object->validate("2004/3/30", "date")
        );
        $this->assertTrue(
            $this->object->validate("2004", "date,,leap")
        );
        $this->assertTrue(
            $this->object->validate("2004/4/29", "date,,leap")
        );
        $this->assertFalse(
            $this->object->validate("2005/4/29", "date,,leap")
        );

        //alnum tests
        $this->assertTrue(
            $this->object->validate("test 123", "alnum")
        );

        $this->assertTrue(
            $this->object->validate("test123", "alnum,,noWhitespace")
        );
        $this->assertTrue(
            $this->object->validate("test", "alnum,,notEmpty")
        );
        $this->assertFalse(
            $this->object->validate("", "alnum,,notEmpty")
        );

        //string tests
        $this->assertTrue(
            $this->object->validate("/echo/echoTest", "string")
        );

        $this->assertFalse(
            $this->object->validate(2, "string")
        );

        //bool tests
        $this->assertTrue(
            $this->object->validate(true, "bool")
        );
        $this->assertTrue(
            $this->object->validate(false, "bool")
        );
        $this->assertFalse(
            $this->object->validate(1, "bool")
        );
        $this->assertFalse(
            $this->object->validate("true", "bool")
        );
        $this->assertFalse(
            $this->object->validate("false", "bool")
        );
    }
}