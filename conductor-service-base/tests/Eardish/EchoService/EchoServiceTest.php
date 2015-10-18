<?php
namespace Eardish\EchoService;


class EchoServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EchoService
     */
    protected $service;
    protected $connection;
    protected $config;
    protected $userId = 2;
    protected $name = "Barry";
    protected $email = "barryallen@gmail.com";


    public function setUp()
    {
        $this->connection = $this->getMockBuilder("Eardish\\EchoService\\Core\\Connection")->getMock();
        $this->service = new EchoService($this->connection);


    }

    public function testPass()
    {
        $this->connection->method('sendToDB')->willReturn(['success' => true]);

        $this->assertTrue(
            $this->service->passEcho($this->userId, $this->name, $this->email)["success"]
        );
    }

    public function testReverse()
    {
        $this->connection->method('sendToDB')->willReturn(
            [
                'success' => true,
                'data' => [
                    0 => [
                        "userId" => 2,
                        "name" =>"Barry",
                        "email" => "barryallen@gmail.com"
                    ]
                ]
            ]);

        $reversedEmail = strrev("barryallen@gmail.com");

        $this->assertEquals(
            $this->service->reverseEcho($this->email), $reversedEmail
        );

    }
}
