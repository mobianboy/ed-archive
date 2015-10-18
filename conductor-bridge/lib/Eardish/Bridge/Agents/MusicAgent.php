<?php
namespace Eardish\Bridge\Agents;

use Eardish\Bridge\Agents\Core\AbstractAgent;

class MusicAgent extends AbstractAgent
{
    protected $addr = "localhost";
    protected $port = "9012";

    public function addTrack($artistProfileId, $trackName, $trackArtId, $requestId)
    {
        $sendArray = $this->arrayGenerator(__FUNCTION__, $requestId);
        $sendArray['params'] = array(
            "artistProfileId" => $artistProfileId,
            "trackArtId" => $trackArtId,
            "trackName" => $trackName
        );

        $this->conn->send($sendArray);
    }

    public function attachAudioS3URL($trackId, $trackUrl, $format, $requestId)
    {
        $sendArray = $this->arrayGenerator(__FUNCTION__, $requestId);
        $sendArray['params'] = array(
            'trackId' => $trackId,
            'trackUrl' => $trackUrl,
            //     'profileId' => $profileId,
            'format' => $format
        );
        $this->conn->send($sendArray);
    }

    public function softDeleteTrack($trackId, $requestId)
    {
        $sendArray = $this->arrayGenerator(__FUNCTION__, $requestId);
        $sendArray['params'] = array(
            'trackId' => $trackId
        );

        $this->conn->send($sendArray);
    }

    public function updateTrack($trackData, $requestId)
    {
        $sendArray = $this->arrayGenerator(__FUNCTION__, $requestId);
        $sendArray['params'] = array(
            'trackData' => $trackData
        );

        $this->conn->send($sendArray);
    }

    public function addTrackGenre($trackId, $genreId, $requestId)
    {
        $sendArray = $this->arrayGenerator(__FUNCTION__, $requestId);
        $sendArray['params'] = array(
            'trackId' => $trackId,
            'genreId' => $genreId
        );

        $this->conn->send($sendArray);
    }

    public function setArtistGenre($profileId, $genreId, $requestId)
    {
        $sendArray = $this->arrayGenerator(__FUNCTION__, $requestId);
        $sendArray['params'] = array(
            'profileId' => $profileId,
            'genreId' => $genreId
        );

        $this->conn->send($sendArray);
    }

    public function getArtistGenre($profileId, $requestId)
    {
        $sendArray = $this->arrayGenerator(__FUNCTION__, $requestId);
        $sendArray['params'] = array(
            'profileId' => $profileId
        );

        $this->conn->send($sendArray);
    }

    public function updateAllGenreTracks($profileId, $genreId, $requestId)
    {
        $sendArray = $this->arrayGenerator(__FUNCTION__, $requestId);
        $sendArray['params'] = array(
            'profileId' => $profileId,
            'genreId' => $genreId
        );

        $this->conn->send($sendArray);
    }

    public function updateArtistGenre($profileId, $genreId, $requestId)
    {
        $sendArray = $this->arrayGenerator(__FUNCTION__, $requestId);
        $sendArray['params'] = array(
            'profileId' => $profileId,
            'genreId' => $genreId
        );

        $this->conn->send($sendArray);
    }

    public function rateTrack($profileId, $trackId, $rating, $requestId)
    {
        $sendArray = $this->arrayGenerator(__FUNCTION__, $requestId);
        $sendArray['params'] = array(
            'profileId' => $profileId,
            'trackId' => $trackId,
            'rating' => $rating
        );

        $this->conn->send($sendArray);
    }

    public function updateTrackRating($profileId, $trackId, $rating, $requestId)
    {
        $sendArray = $this->arrayGenerator(__FUNCTION__, $requestId);
        $sendArray['params'] = array(
            'profileId' => $profileId,
            'trackId' => $trackId,
            'rating' => $rating
        );
        
        $this->conn->send($sendArray);
    }

    public function getTrackDetail($trackId, $requestId)
    {
        $sendArray = $this->arrayGenerator(__FUNCTION__, $requestId);
        $sendArray['params'] = array(
            'trackId' => $trackId
        );

        $this->conn->send($sendArray);
    }

    public function getProfileTrackRating($trackId, $profileId, $requestId)
    {
        $sendArray = $this->arrayGenerator(__FUNCTION__, $requestId);
        $sendArray['params'] = array(
            'trackId' => $trackId,
            'profileId' => $profileId
        );

        $this->conn->send($sendArray);
    }

    public function getCFurl($trackId, $mediaType, $requestId)
    {
        $sendArray = $this->arrayGenerator(__FUNCTION__, $requestId);
        $sendArray['params'] = [
            'trackId' => $trackId,
            'mediaType' => $mediaType
        ];

        $this->conn->send($sendArray);
    }

    public function listGenres($requestId)
    {
        $sendArray = $this->arrayGenerator(__FUNCTION__, $requestId);
        $this->conn->send($sendArray);
    }

    public function createAlbum($profileId, $albumName, $requestId)
    {
        $sendArray = $this->arrayGenerator(__FUNCTION__, $requestId);
        $sendArray['params'] = array(
            'profileId' => $profileId,
            'albumName' => $albumName
        );

        $this->conn->send($sendArray);
    }
}
