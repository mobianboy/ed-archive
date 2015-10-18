<?php
namespace Eardish\Bridge\Controllers;

class UserControllerTest extends ControllerTestCase
{
    /**
     * @var UserController
     */
    protected $controller;
    protected $connection2;

    public function setUp()
    {
        parent::setUp();

        $this->dto->getDataBlock()->setDataArray(array(
            'password' => 'eardishrocks',
            'passwordConfirmation' => 'eardishrocks',
            'email' => 'test@eardish.com',
            'name' => array(
                'first' => 'senor',
                'last' => 'dish'
            ),
            'yearOfBirth' => 1965,
            'zipcode' => '95607',
            'inviteCode' => 'k6g77d3'
        ));
    }

    /**
     * @expectedException \Exception
     */
    public function testNewUserFailPassword()
    {
        $this->dto->getRouteBlock()->setControllerMethod("newUser");
        $this->dto->getRouteBlock()->setControllerName("User");
        $this->dto->getDataBlock()->setDataArray(array(
            'password' => 'eardishrocks',
            'passwordConfirmation' => 'eardishrules'
        ));
        $bridgeKernel = $this->newBridgeKernel();
        $bridgeKernel->inbound($this->dto);
    }

    public function testNewUser()
    {

        $this->dto->getRouteBlock()->setControllerMethod("newUser");
        $this->dto->getRouteBlock()->setControllerName("User");
        $this->connection->expects($this->any())->method('send')
            ->will($this->onConsecutiveCalls(
                array('hashPass' => '5ewrekdsf'),
                array('userID' => 19),
                array('contactId' => 23),
                array('data' => array( 0 => array(
                    'id' => 18,
                    'email' => 'test@eardish.com'
                ))),
                true,
                true
            ));

        $bridgeKernel = $this->newBridgeKernel();

        $this->assertEquals(
            array(
                'data' => array(
                    'profileId' => 18,
                    'userId' => 19
                )
            ),
            $bridgeKernel->inbound($this->dto)
        );
    }

    public function testForgotPassword()
    {
        $this->dto->getRouteBlock()->setControllerMethod("forgotPassword");
        $this->dto->getRouteBlock()->setControllerName("User");
        $this->dto->getDataBlock()->setDataArray(array(
            'email' => 'waaffles@gmail.com',
            'genres-disliked' => ['Country'],
        ));

        $this->connection->expects($this->any())->method('send')
            ->will($this->onConsecutiveCalls(
                ['resetPasscode' => 'c6uid03'],
                ['data' => array(0 => array(
                    'first_name' => 'Peter',
                    'last_name' => 'Parker'
                    ))
                ],
                ['success' => true]
            ));
        $bridgeKernel = $this->newBridgeKernel();

        $this->assertEquals(
            array('data' => array()),
            $bridgeKernel->inbound($this->dto)
        );
    }

    /**
     * @expectedException \Exception
     */
    public function testInviteFriendAllCodesUsed()
    {
        $this->dto->getMetaBlock()->setCurrentUser(2);
        $this->dto->getRouteBlock()->setControllerMethod("inviteFriend");
        $this->dto->getRouteBlock()->setControllerName("User");

        $this->connection->expects($this->any())->method('send')
            ->will($this->onConsecutiveCalls(
                5
            ));
        $bridgeKernel = $this->newBridgeKernel();
        $bridgeKernel->inbound($this->dto);
    }

    public function testInviteFriend()
    {
        $this->dto->getMetaBlock()->setCurrentUser(2);

        $this->connection->expects($this->any())->method('send')
            ->will($this->onConsecutiveCalls(
                3,
                ['inviteCode' => 3],
                ['userID' => 'A5FC3'],
                true,
                true
            ));

        $this->dto->getRouteBlock()->setControllerMethod("inviteFriend");
        $this->dto->getRouteBlock()->setControllerName("User");

        $bridgeKernel = $this->newBridgeKernel();

        $this->assertEquals(
            array('data' => array()),
            $bridgeKernel->inbound($this->dto)
        );
    }

}
