<?php
namespace Eardish\UserService;

use Eardish\AppConfig;

class UserServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UserService
     */
    protected $service;
    protected $connection;
    protected $config;

    public function setUp()
    {
        $this->connection = $this->getMockBuilder("Eardish\\UserService\\Core\\Connection")->getMock();
        $config = new AppConfig('/eda/secret/app.json', 'local');
        $this->service = new UserService($this->connection, $config);
    }

    public function testCreateUserFailed()
    {
        $this->connection->method('sendToDB')
            ->willReturn(
                [
                    'success' => false
                ]
            );

        $this->service->setPriority("10");
        $this->assertEquals(
            array('userID' => false),
            $this->service->createUser('test@eardish.com', "password")
        );
    }

    public function testCreateUserSuccess()
    {
        $this->connection->method('sendToDB')
            ->willReturn(
                [
                    'success' => true,
                    'count' => 1,
                    'data' => [
                        0 => [
                            'id' => 14
                        ]
                    ],
                ]
            );

        $this->assertEquals(
            array('userID' => 14),
            $this->service->createUser('test@eardish.com', "password")
        );
    }

    public function testCreateUserProfileFailed()
    {
        $this->connection->method('sendToDB')
            ->willReturn(
                [
                    'success' => false
                ]
            );

        $this->assertEquals(
            array('profileID' => false),
            $this->service->createUserProfile(10, 'senor', 'eardish', 1988, '91423')
        );
    }

    public function testCreateUserProfileSuccess()
    {
        $this->connection->method('sendToDB')
            ->willReturn(
                [
                    'success' => true,
                    'count' => 1,
                    'data' => [
                        0 => [
                            'id' => 19
                        ]
                    ]
                ]
            );

        $this->assertEquals(
            array('profileID' => 19),
            $this->service->createUserProfile(10, 'senor', 'eardish', 1988, '91423')
        );
    }

    public function testInvitesUsedSuccess()
    {
        $this->connection->method('sendToDB')->willReturn(
            [
                'success' => true,
                'data' => [
                        0 => ['count' => "4"]
                    ]
            ]);

        $this->assertEquals(
            ['invitesUsed' => 4],
            $this->service->invitesUsed(10)
        );
    }

    public function testInvitesUsedFail()
    {
        $this->connection->method('sendToDB')->willReturn(
            [
                'success' => false,
            ]);

        $this->assertEquals(
            array('invitesUsed' => -1),
            $this->service->invitesUsed(10)
        );
    }

    public function testRegisterInviteCodeSuccess()
    {
        $this->connection->method('sendToDB')
            ->willReturn(
                [
                    'success' => true,
                    'count' => 1,
                    'data' => [
                        0 => [
                            'id' => 19,
                            'inviter_id' => 6,
                            'invitee_id' => 10,
                            'invite_code' => '4ys6xdv'
                        ]
                    ]
                ]
            );

        $this->assertEquals(
            array('invite-code' => '4ys6xdv'),
            $this->service->registerInviteCode(10, '4ys6xdv', 'test@eardish.com')
        );
    }

    public function testRegisterInviteCodeFail()
    {
        $this->connection->method('sendToDB')
            ->willReturn(
                [
                    'success' => false
                ]
            );

        $this->assertEquals(
            array('invite-code' => 0),
            $this->service->registerInviteCode(10, '4ys6xdv', 'test@eardish.com')
        );
    }

    public function testCreateInviteCode()
    {
        $codes = array();
        for ($i = 0; $i <= 20; $i++) {
            $codes[] = $this->service->createInviteCode();
            if ($i > 0) {
                $this->assertNotEquals(
                    $codes[$i],
                    $codes[$i - 1]
                );
            }
        }
    }

    public function testValidateInviteCodeSuccess()
    {
        $this->connection->method('sendToDB')
            ->willReturn(
                [
                    'success' => true,
                    'count' => 1,
                    'data' => [
                        0 => [
                            'id' => 19,
                            'inviter_id' => 6,
                            'invitee_id' => null,
                            'invite_code' => '4ys6xdv'
                        ]
                    ]
                ]
            );

        $this->assertEquals(
            ['data' => ['id' => 19, 'invite_code' => "4ys6xdv", 'invitee_id' => null, 'inviter_id' => 6]],
            $this->service->validateInviteCode('4ys6xdv')
        );
    }

    public function testValidateInviteCodeFail()
    {
        $this->connection->method('sendToDB')
            ->willReturn(
                [
                    'success' => false
                ]
            );

        $this->assertEquals(
            array(
                'data' => []
            ),
            $this->service->validateInviteCode('4ys6xdv')
        );
    }

    public function testValidateInviteCodeUsedAlready()
    {
        $this->connection->method('sendToDB')
            ->willReturn(
                [
                    'success' => true,
                    'count' => 1,
                    'data' => [
                        0 => [
                            'id' => 19,
                            'inviter_id' => 6,
                            'invitee_id' => 12,
                            'invite_code' => '4ys6xdv'
                        ]
                    ]
                ]
            );

        $this->assertEquals(
            array(
                'data' => []
            ),
            $this->service->validateInviteCode('4ys6xdv')
        );
    }

    public function testConfigArrayGenerator()
    {
        $this->service->setPriority("10");
        $config = array(
            'operation' => 'select',
            'priority' => '10',
            'request' => 'validateInviteCode',
            'service' => 'UserService'
        );

        $this->assertEquals(
            $config,
            $this->service->generateConfigArray('validateInviteCode', 'select')
        );
    }

    public function testCheckIfEmailAlreadyExistsSuccess()
    {
        $this->connection->method('sendToDB')
            ->willReturn(
                [
                    'count' => 1
                ]
            );

        $this->assertEquals(
            ['email-exists' => true],
            $this->service->checkIfEmailAlreadyExists('test2@eardish.com')
        );

    }

    public function testCheckIfEmailAlreadyExistsFail()
    {
        $this->connection->method('sendToDB')
            ->willReturn(
                [
                    'count' => 0
                ]
            );

        $this->assertEquals(
            ['email-exists' => false],
            $this->service->checkIfEmailAlreadyExists('test2@eardish.com')
        );

    }

    public function testHasExtraInvites()
    {
        $this->connection->method('sendToDB')->willReturn(
            [
                'success' => true,
                'count' => 1,
                'data' => [
                    0 => ['extra_invites' => 10]
                ]
            ]);

        $this->assertEquals(
            ['extraInvites' => 10],
            $this->service->extraInvites(5)
        );
    }

    public function testHasNoExtraInvites()
    {
        $this->connection->method('sendToDB')->willReturn(
            [
                'success' => true,
                'count' => 1,
                'data' => [
                    0 => ['extra_invites' => null]
                ]
            ]);

        $this->assertEquals(
            ['extraInvites' => 0],
            $this->service->extraInvites(5)
        );
    }

    public function testRedeemInviteCode()
    {
        $this->connection->method('sendToDB')->willReturn(
            [
                'success' => true,
                'data' => [
                    0 => ['id' => 2,
                    'inviter_id' => 5,
                    'invitee_id' => 7,
                    'invite_code' => "e61d0d55",
                    'invitee_email' => "testemail@eardish.com"
                ]],
            ]);

        $this->assertEquals(
            ['data' => ['id' => 2, 'inviter_id' => 5, 'invitee_id' => 7, 'invite_code' => "e61d0d55", 'invitee_email' => "testemail@eardish.com"]],
            $this->service->redeemInviteCode(5, 7)
        );
    }

    public function testUpdateExtraInvites()
    {
        $this->connection->method('sendToDB')->willReturn(
            [
                'success' => true,
                'count' => 1,
                'data' => [
                    0 => ['extra_invites' => 10]
                ]
            ]);

        $this->assertEquals(
            ['data' => ['extraInvites' => 10]],
            $this->service->updateExtraInvites(5, 10)
        );
    }
}
