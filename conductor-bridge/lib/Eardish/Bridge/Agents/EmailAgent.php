<?php
namespace Eardish\Bridge\Agents;

use Eardish\Bridge\Agents\Core\AbstractAgent;

class EmailAgent extends AbstractAgent
{
    public function sendInviteCode($emails, $inviteCode, $name, $requestId)
    {
        $sendArray = $this->arrayGenerator(__FUNCTION__, $requestId);
        $sendArray['params'] = array(
            'emails' => $emails,
            'inviteCode' => $inviteCode,
            'name' => $name
        );

        $this->conn->send($sendArray);
    }

    public function sendResetPassCode($emails, $newPassCode, $name, $requestId)
    {
        $sendArray = $this->arrayGenerator(__FUNCTION__, $requestId);
        $sendArray['params'] = array(
            'emails' => $emails,
            'newPassCode' => $newPassCode,
            'name' => $name
        );

        $this->conn->send($sendArray);
    }

    public function sendBadgeWinnerList($emails, $winners, $weekStart, $weekEnd, $requestId)
    {
        $sendArray = $this->arrayGenerator(__FUNCTION__, $requestId);
        $sendArray['params'] = array(
            'emails' => $emails,
            'winners' => $winners,
            'weekStart' => $weekStart,
            'weekEnd' => $weekEnd
        );

        $this->conn->send($sendArray);
    }
}