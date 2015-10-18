<?php
namespace Eardish\Bridge\Agents;

use Eardish\Bridge\Agents\Core\AbstractAgent;

class UserAgent extends AbstractAgent
{
    public function createUser($email, $password, $requestId)
    {
        $sendArray = $this->arrayGenerator(__FUNCTION__, $requestId);
        $sendArray['params'] = array(
            'email' => $email,
            'password' => $password
        );

        $this->conn->send($sendArray);
    }

    public function redeemInviteCode($userId, $inviteId, $requestId)
    {
        $sendArray = $this->arrayGenerator(__FUNCTION__, $requestId);
        $sendArray['params'] = [
            'userId' => $userId,
            'inviteId' => $inviteId
        ];

        $this->conn->send($sendArray);
    }

    public function checkIfEmailAlreadyExists($email, $requestId)
    {
        $sendArray = $this->arrayGenerator(__FUNCTION__, $requestId);
        $sendArray['params'] = [
            'email' => $email
        ];
        $this->conn->send($sendArray);
    }

    public function validateInviteCode($inviteCode, $requestId)
    {
        $sendArray = $this->arrayGenerator(__FUNCTION__, $requestId);
        $sendArray['params'] = [
            'inviteCode' => $inviteCode
        ];

        $this->conn->send($sendArray);
    }

    public function createInviteCode($requestId)
    {
        $sendArray = $this->arrayGenerator(__FUNCTION__, $requestId);

        $this->conn->send($sendArray);
    }

    public function invitesUsed($userId, $requestId)
    {
        $sendArray = $this->arrayGenerator(__FUNCTION__, $requestId);
        $sendArray['params'] = [
            'userId' => $userId
        ];

        $this->conn->send($sendArray); //return result['invitesUsed'] blah blah
    }

    public function extraInvites($userId, $requestId)
    {
        $sendArray = $this->arrayGenerator(__FUNCTION__, $requestId);
        $sendArray['params'] = [
            'userId' => $userId
        ];

        $this->conn->send($sendArray);
    }

    public function updateExtraInvites($userId, $invitesLeft, $requestId)
    {
        $sendArray = $this->arrayGenerator(__FUNCTION__, $requestId);
        $sendArray['params'] = [
            'userId' => $userId,
            'invitesLeft' => $invitesLeft
        ];

        $this->conn->send($sendArray);
    }

    public function registerInviteCode($userId, $inviteCode, $email, $requestId)
    {
        $sendArray = $this->arrayGenerator(__FUNCTION__, $requestId);

        $sendArray['params'] = [
            'userId' => $userId,
            'inviteCode' => $inviteCode,
            'inviteeEmail' => $email
        ];

        $this->conn->send($sendArray);
    }
}
