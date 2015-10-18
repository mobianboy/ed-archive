<?php
namespace Eardish\Bridge\Traits;

trait OptionalParams {

    /**
     * @param $dataArray array
     * @return array
     */
    public function getClientProfileData(array $dataArray)
    {
        $profileData = array();

        //Profile
        $profileData['data']['profile']['id'] = $this->checkOptional('id', $dataArray);
        $profileData['data']['profile']['type'] = $this->checkOptional('type', $dataArray);
        $profileData['data']['profile']['bio'] = $this->checkOptional('bio', $dataArray);
        $profileData['data']['profile']['artist_name'] = $this->checkOptional('artistName', $dataArray);
        $profileData['data']['profile']['hometown'] = $this->checkOptional('hometown', $dataArray);
        $profileData['data']['profile']['year_of_birth'] = $this->checkOptional('yearOfBirth', $dataArray);
        $profileData['data']['profile']['invite_code'] = $this->checkOptional('inviteCode', $dataArray);

        if ($this->checkOptional('name', $dataArray)) {
            $profileData['data']['profile']['first_name'] = $this->checkOptional('first', $dataArray['name']);
            $profileData['data']['profile']['last_name'] = $this->checkOptional('last', $dataArray['name']);
        }
        $profileData['data']['profile']['ar_rep'] = $this->checkOptional('arRep', $dataArray);
        $profileData['data']['profile']['influenced_by'] = $this->checkOptional('influencedBy', $dataArray);
        $profileData['data']['profile']['year_founded'] = $this->checkOptional('yearFounded', $dataArray);
        $profileData['data']['profile']['website'] = $this->checkOptional('website', $dataArray);
        $profileData['data']['profile']['facebook_page'] = $this->checkOptional('facebookPage', $dataArray);
        $profileData['data']['profile']['twitter_page'] = $this->checkOptional('twitterPage', $dataArray);
        $profileData['data']['profile']['contact_id'] = $this->checkOptional('contactId', $dataArray);

        //ART
        if (!empty($dataArray['art'])) {
            $profileData['data']['art']['description'] = $this->checkOptional('description', $dataArray['art']);
            $profileData['data']['art']['title'] = $this->checkOptional('title', $dataArray['art']);
            $profileData['data']['art']['type'] = $this->checkOptional('type', $dataArray['art']);
            $profileData['data']['image']['url'] = $this->checkOptional('url', $dataArray['art']);
        }

        //Profile Genre
        $profileData['data']['profile_genre']['genre_id'] = $this->checkOptional('genre', $dataArray);

        //AR Rep
        $profileData['data']['ar_rep']['ar_rep'] = $this->checkOptional('arRep', $dataArray);

        //Contact
        $profileData['data']['contact']['id'] = $this->checkOptional('contactId', $dataArray);
        $profileData['data']['contact']['phone'] = $this->checkOptional('phone', $dataArray);
        $profileData['data']['contact']['address1'] = $this->checkOptional('address1', $dataArray);
        $profileData['data']['contact']['address2'] = $this->checkOptional('address2', $dataArray);
        $profileData['data']['contact']['city'] = $this->checkOptional('city', $dataArray);
        $profileData['data']['contact']['state'] = $this->checkOptional('state', $dataArray);
        $profileData['data']['contact']['zipcode'] = $this->checkOptional('zipcode', $dataArray);

        return $profileData;
    }

    /**
     * @param $dataArray array
     * @return array
     */
    public function getClientTrackData(array $dataArray)
    {
        $trackData = array();
        $trackData['data']['track']['published'] = $this->checkOptional('published', $dataArray);

        return $trackData;
    }

    public function hasContactData($contactArray)
    {
        foreach ($contactArray as $key => $value) {
            if ($value) {
                return true;
            }
        }

        return false;
    }

    //Helper function
    public function checkOptional($key, $array, $default = null)
    {
        return array_key_exists($key, $array) ? $array[$key] : $default;
    }
}