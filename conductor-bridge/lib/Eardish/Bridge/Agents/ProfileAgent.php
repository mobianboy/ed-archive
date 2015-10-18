<?php
namespace Eardish\Bridge\Agents;

use Eardish\Bridge\Agents\Core\AbstractAgent;

class ProfileAgent extends AbstractAgent
{
    public function getFullNameByEmail($email, $requestId)
    {
        $sendArray = $this->arrayGenerator(__FUNCTION__, $requestId);
        $sendArray['params'] = array(
            'email' => $email
        );

        $this->conn->send($sendArray);
    }

    public function createProfile($params, $requestId)
    {
        $sendArray = $this->arrayGenerator(__FUNCTION__, $requestId);

        $sendArray['params'] = [
            'profileData' => $params
        ];

        $this->conn->send($sendArray);
    }

    public function updateProfileIsOnboarded($profileId, $requestId)
    {
        $sendArray = $this->arrayGenerator(__FUNCTION__, $requestId);

        $sendArray['params'] = [
            'profileId' => $profileId
        ];

        $this->conn->send($sendArray);
    }

    public function selectContactInfo($contactId, $requestId)
    {
        $sendArray = $this->arrayGenerator(__FUNCTION__, $requestId);

        $sendArray['params'] = [
            'contactId' => $contactId
        ];

        $this->conn->send($sendArray);
    }

    public function createContactInfo($params, $requestId)
    {
        $sendArray = $this->arrayGenerator(__FUNCTION__, $requestId);

        $sendArray['params'] = [
            'contactData' => $params
        ];

        $this->conn->send($sendArray);
    }

    public function editContactInfo($params, $requestId)
    {
        $sendArray = $this->arrayGenerator(__FUNCTION__, $requestId);

        $sendArray['params'] = [
            'contactData' => $params
        ];

        $this->conn->send($sendArray);
    }

    public function getArtistContent($profileId, $requestId)
    {
        $sendArray = $this->arrayGenerator(__FUNCTION__, $requestId);

        $sendArray['params'] = [
            'profileId' => $profileId
        ];

        $this->conn->send($sendArray);
    }

    public function selectProfile($profileId, $requestId)
    {
        $sendArray = $this->arrayGenerator(__FUNCTION__, $requestId);
        $sendArray['params'] = [
            'profileId' => $profileId
        ];

        $this->conn->send($sendArray);
    }

    public function formatProfileResponse($profileResponseFlat, $contact = false)
    {
        $profileFormatted = array();
        $socialKeys = array('facebook_page', 'twitter_page');
        $contactKeys = array('phone', 'address1', 'address2', 'city', 'state', 'zipcode');
        $nameKeys = array('first_name' => 'first', 'last_name' => 'last');

        foreach ($profileResponseFlat as $columnName => $value) {

            if (in_array($columnName, $socialKeys)) {
                $profileFormatted['socialLinks'][$columnName] = $value;
            } elseif (in_array($columnName, $contactKeys)) {
                $profileFormatted['address'][$columnName] = $value;
            } elseif (array_key_exists($columnName, $nameKeys)) {
                $profileFormatted['name'][$nameKeys[$columnName]] = $value;
            } else {
                $profileFormatted[$columnName] = $value;
            }
        }

        if (!$contact) {
            if ($profileResponseFlat['artist_name']) {
                $profileFormatted['displayName'] = $profileResponseFlat['artist_name'];
            } else {
                $profileFormatted['displayName'] = $profileResponseFlat['first_name'] . " " . $profileResponseFlat['last_name'];
            }
        }

        return $profileFormatted;
    }

    public function listArtistProfiles($requestId)
    {
        $sendArray = $this->arrayGenerator(__FUNCTION__, $requestId);

        $this->conn->send($sendArray);
    }

    public function getArtUrls($artId, $requestId)
    {
        $sendArray = $this->arrayGenerator(__FUNCTION__, $requestId);
        $sendArray['params'] = [
            'artId' => $artId
        ];

        $this->conn->send($sendArray);

    }

    public function editArtistProfile($profileData, $requestId)
    {
        $sendArray = $this->arrayGenerator(__FUNCTION__, $requestId);
        $sendArray['params'] = array(
            'profileData' => $profileData
        );

        $this->conn->send($sendArray);
    }
}
