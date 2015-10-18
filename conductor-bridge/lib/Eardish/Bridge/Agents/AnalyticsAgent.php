<?php
namespace Eardish\Bridge\Agents;

use Eardish\Bridge\Agents\Core\AbstractAgent;

class AnalyticsAgent extends AbstractAgent
{

    // This chart is configurable (pass in track_id or artist_id to group_by)
    public function getCompletedListensChart($durationStart, $durationStop, $groupBy, $requestId)
    {
        $sendArray = $this->arrayGenerator(__FUNCTION__, $requestId);

        $sendArray['params'] = [
            'start' => $durationStart,
            'stop' => $durationStop,
            'groupBy' => $groupBy
        ];
        $this->conn->send($sendArray);
    }

    public function getCompletedListensFans($durationStart, $durationStop, $requestId)
    {
        $sendArray = $this->arrayGenerator(__FUNCTION__, $requestId);

        $sendArray['params'] = [
            'start' => $durationStart,
            'stop' => $durationStop
        ];

        $this->conn->send($sendArray);
    }


    public function getMostTracksRatedFans($durationStart, $durationStop, $requestId)
    {
        $sendArray = $this->arrayGenerator(__FUNCTION__, $requestId);

        $sendArray['params'] = [
            'start' => $durationStart,
            'stop' => $durationStop
        ];

       $this->conn->send($sendArray);
    }

    // This chart is configurable (pass in track_id or artist_id to group_by)
    public function getHighestRatedChart($durationStart, $durationStop, $groupBy, $requestId)
    {
        $sendArray = $this->arrayGenerator(__FUNCTION__, $requestId);

        $sendArray['params'] = [
            'start' => $durationStart,
            'stop' => $durationStop,
            'groupBy' => $groupBy
        ];

        $this->conn->send($sendArray);
    }

    public function distributeBadges($badgeData, $requestId)
    {
        $sendArray = $this->arrayGenerator(__FUNCTION__, $requestId);

        $sendArray['params'] = [
            'badgeWinners' => $badgeData
        ];

        $this->conn->send($sendArray);
    }

    public function getBadges($requestId)
    {
        $sendArray = $this->arrayGenerator(__FUNCTION__, $requestId);

        $this->conn->send($sendArray);
    }

    public function getProfileBadges($profileId, $requestId)
    {
        $sendArray = $this->arrayGenerator(__FUNCTION__, $requestId);

        $sendArray['params'] = [
            'profileId' => $profileId
        ];

        $this->conn->send($sendArray);
    }

    public function getCompletedListens($userId, $event, $durationStart, $durationStop, $requestId)
    {
        $sendArray = $this->arrayGenerator(__FUNCTION__, $requestId);

        $sendArray['params'] = [
            'userId' => $userId,
            'event' => $event,
            'start' => $durationStart,
            'stop' => $durationStop
        ];

        $this->conn->send($sendArray);
    }

    public function getRatedTracks($profileId, $durationStart, $durationStop, $requestId)
    {;
        $sendArray = $this->arrayGenerator(__FUNCTION__, $requestId);

        $sendArray['params'] = [
            'profileId' => $profileId,
            'start' => $durationStart,
            'stop' => $durationStop
        ];

        $this->conn->send($sendArray);
    }

    public function submitEntry($data, $requestId)
    {
        $config = $this->arrayGenerator(__FUNCTION__, $requestId);

        $config['params']['data'] = $data;

        $this->conn->send($config);
    }
}