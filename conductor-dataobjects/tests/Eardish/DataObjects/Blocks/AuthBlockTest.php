<?php
namespace Eardish\DataObjects\Blocks;

class AuthBlockTest extends \PHPUnit_Framework_TestCase
{
    public function testGetPass()
    {
        $auth = new AuthBlock("test@eardish.com", "passw0Rd");

        $this->assertEquals(
            "passw0Rd",
            $auth->getPassword()
        );

        $auth->setPassword("pass2");

        $this->assertEquals(
            "pass2",
            $auth->getPassword()
        );
    }

    public function testGetUser()
    {
        $auth = new AuthBlock("test@eardish.com", "pass");

        $this->assertEquals(
            "test@eardish.com",
            $auth->getEmail()
        );

        $auth->setEmail("user@eardish.com");

        $this->assertEquals(
            "user@eardish.com",
            $auth->getEmail()
        );
    }

    public function testIsAuthable()
    {
        $auth = new AuthBlock("test@eardish.com", "pass");

        $this->assertTrue(
            $auth->isAuthable()
        );

        $auth2 = new AuthBlock("test@eardish.com", "");

        $this->assertFalse(
            $auth2->isAuthable()
        );

        $auth3 = new AuthBlock("", "pass");

        $this->assertFalse(
            $auth3->isAuthable()
        );

        $auth4 = new AuthBlock("", "");

        $this->assertFalse(
            $auth4->isAuthable()
        );
    }
}
