<?php
namespace Eardish\AuthService;

use Eardish\AppConfig;

class AuthServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AuthService
     */
    protected $authService;
    protected $connection;
    protected $options;

    public function setUp()
    {
        $this->connection = $this->getMockBuilder("Eardish\\AuthService\\Core\\Connection")->getMock();
        $config = new AppConfig('/eda/secret/app.json', 'local');
        $this->authService = new AuthService($this->connection, $config);
        $this->options = array('cost' => 11);
    }

    public function testGetHashPass()
    {
        $this->connection->expects($this->any())->method('sendToDB')
            ->will($this->onConsecutiveCalls(
                [
                    'success' => true,
                    'data' => [
                        0 => [
                            'password' => 'TestPassword'
                        ]
                    ]

                ]
            ));

        $this->assertEquals(
            ['password' => "TestPassword"],
            $this->authService->getHashPass('test@eardish.com', "TestPassword")
        );
    }

    public function testAuthenticate()
    {
        $hashPass = '$2y$11$jXwy.6RjJMur.Ni/MoL1cuvVpv/lExFcqxJOHPrOwECkgqSluc/9W';

        //echo password_hash("TestPassword", PASSWORD_DEFAULT, array('cost' => 11));

        $this->connection->method('sendToDB')->will($this->onConsecutiveCalls(
            [
                "success" => true,
                "data" => [
                    0 => ["password" => $hashPass]]
            ],
            [
                "success" => true,
                "data" => [
                    0 => ["id" => 10]]
            ],
            [
                "success" => true,
                "data" => [
                    0 => [
                            "id" => 10,
                            "onboarded" => null]]
            ]
        ));

        $this->assertEquals(
            ["success" => true, "userId" => 10, "profileId" => 10, "onboarded" => null],
            $this->authService->authenticate('email@eardish.com', 'TestPassword')
        );
    }

    public function testGenerateResetPassCode()
    {
        $this->connection->method('sendToDB')->will($this->onConsecutiveCalls(
            [
                "success" => true,
                "data" => [
                    0 => [
                        "reset_passcode" => 'a0b1c2d3']]
            ]
        ));

        $this->assertEquals(
            ['data' => ['resetPasscode' => "a0b1c2d3"]],
            $this->authService->generateResetPassCode('email@eardish.com')
        );
    }

    public function testDeleteResetPassCode()
    {
        $this->connection->method('sendToDB')->will($this->onConsecutiveCalls(
            [
                "success" => true,
                "data" => [
                    0 => [
                        "reset_passcode" => 'a0b1c2d3e4']]
            ]
        ));

        $this->assertEquals(
            ['reset_passcode' => "a0b1c2d3e4"],
            $this->authService->deleteResetPassCode('email@eardish.com')
        );
    }

    public function testGetEmailByResetCode()
    {
        $this->connection->method('sendToDB')->will($this->onConsecutiveCalls(
            [
                "success" => true,
                "data" => [
                    0 => [
                        "email" => 'test@eardish.com']]
            ]
        ));

        $this->assertEquals(
            ['email' => 'test@eardish.com'],
            $this->authService->getEmailByResetCode('a0b1c2d3e4')
        );
    }

    public function testUpdatePassword()
    {
        $this->connection->method('sendToDB')->will($this->onConsecutiveCalls(
            [
                "success" => true,
                "password" => 'password2'
            ]
        ));

        $this->assertEquals(
            ["password" => 'password2'],
            $this->authService->updatePassword('test@eardish.com', 'password2')
        );
    }


//    /**
//     * @codeCoverageIgnore
//     */
//    public function testUpdatePassword()
//    {
//    // TODO create test for successful PG write/status code back from DB service, get mock connection
//    }

}
