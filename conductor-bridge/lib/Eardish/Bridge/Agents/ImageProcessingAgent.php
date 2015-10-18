<?php
namespace Eardish\Bridge\Agents;

use Eardish\Bridge\Agents\Core\AbstractAgent;

class ImageProcessingAgent extends AbstractAgent
{
    public function addArt($profileId, $title, $type, $url, $description, $requestId)
    {
        $sendArray = $this->arrayGenerator(__FUNCTION__, $requestId);

        $sendArray['params'] = array(
            'profileId' => $profileId,
            'title' => $title,
            'type' => $type,
            'url' => $url,
            'description' => $description,
            'sizes' => [
                "profile_art_phone_small" => 250,
                "profile_art_phone_large" => 500,
                "profile_art_tablet_small" => 500,
                "profile_art_tablet_large" => 750,
                "profile_art_thumbnail_small" => 50,
                "profile_art_thumbnail_large" => 100
            ]
        );

        $this->conn->send($sendArray);
    }
}
