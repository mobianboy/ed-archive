<?php
namespace Eardish\Bridge\Agents;

use Eardish\Bridge\Agents\Core\AbstractAgent;

class RecommendationAgent extends AbstractAgent
{
    protected $addr = "localhost";
    protected $port = "9014";

    public function modifyProfileGenreBlend($profileId, $genreIds, $requestId)
    {
        $sendArray = $this->arrayGenerator(__FUNCTION__, $requestId);
        $sendArray['params'] = array(
            'profileId' => $profileId,
            'genreIds' => array_keys($genreIds),
            'weights' => array_values($genreIds)
        );

        $this->conn->send($sendArray);
    }

    public function getProfileGenreBlend($profileId, $requestId)
    {
        $sendArray = $this->arrayGenerator(__FUNCTION__, $requestId);
        $sendArray['params'] = array(
            'profileId' => $profileId
        );

        $this->conn->send($sendArray);
    }

    // TODO Iterate on this, currently only dummy function
    public function getSingleGenreBlendSet($profileId, $genreId, $limit, $requestId)
    {
        $sendArray = $this->arrayGenerator(__FUNCTION__, $requestId);
        $sendArray['params'] = array(
            'profileId' => $profileId,
            'genreId' => $genreId,
            'limit' => $limit
        );

        $this->conn->send($sendArray);
    }

    // TODO Iterate on this, currently only dummy function
    public function getFullGenreBlendSet($profileId, $limit, $requestId)
    {
        $sendArray = $this->arrayGenerator(__FUNCTION__, $requestId);
        $sendArray['params'] = array(
            'profileId' => $profileId,
            'limit' => $limit
        );

        $this->conn->send($sendArray);
    }
}
