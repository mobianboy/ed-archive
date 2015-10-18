<?php
namespace Eardish\Bridge\Agents;

use Eardish\Bridge\Agents\Core\AbstractAgent;

class AuthAgent extends AbstractAgent
{
    public function generateResetPassCode($email, $requestId)
    {
        $sendArray = $this->arrayGenerator(__FUNCTION__, $requestId);
        $sendArray['params'] = array(
            'email' => $email
        );
        $this->conn->send($sendArray);
    }

    public function hashPass($password, $requestId)
    {
        $sendArray = $this->arrayGenerator(__FUNCTION__, $requestId);
        $sendArray['params'] = array(
            'password' => $password
        );

        $this->conn->send($sendArray);
    }

    public function getEmailByResetCode($resetCode, $requestId)
    {
        $sendArray = $this->arrayGenerator(__FUNCTION__, $requestId);
        $sendArray['params'] = array(
            'resetCode' => $resetCode
        );

        $this->conn->send($sendArray);
    }

    public function updatePassword($email, $password, $requestId)
    {
        $sendArray = $this->arrayGenerator(__FUNCTION__, $requestId);
        $sendArray['params'] = array(
            'email' => $email,
            'password' => $password,
        );

        $this->conn->send($sendArray);
    }

    public function getClientAws($requestId)
    {
        $data = $this->arrayGenerator(__FUNCTION__, $requestId);

        $this->conn->send($data);
    }
}