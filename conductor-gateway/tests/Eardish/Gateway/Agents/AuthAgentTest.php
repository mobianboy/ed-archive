<?php
namespace Eardish\Gateway\Agents;

class AuthAgentTest extends AppConfigTest
{

    /**
     * @var AuthAgent
     */
    protected $agent;

    public function setUp()
    {
        parent::setUp();

        $conn = $this->getMockBuilder('Eardish\Gateway\Agents\Core\Connection')
            ->getMock();

        $conn->method('send')
            ->will($this->onConsecutiveCalls(
                ["success" => true, "userId" => 12, "profileId" => 12],
                ["success" => false]
            )
            );

        $this->agent = new AuthAgent($conn, $this->appConfig);
    }

    public function testAuthenticate()
    {

//        var_dump($this->agent->authenticate("gooduser", "goodpass"));

        $this->assertEquals(
            ["success" => true, "userId" => 12, "profileId" => 12],
            $this->agent->authenticate("gooduser", "goodpass")
        );

        $this->assertEquals(
            [],
            $this->agent->authenticate("baduser", "badpass")
        );

    }
}