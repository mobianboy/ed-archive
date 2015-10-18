<?php
namespace Eardish\Bridge\Traits;

trait ProfileFormatter {

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

    public function formatBadges($badgeResponse) {
        if (!$badgeResponse) {
            return [];
        }

        $badges['badges'] = [];

        foreach ($badgeResponse as $badge) {
            if (isset($badges['badges'][$badge['badge_id']])) {
                $badges['badges'][$badge['badge_id']]++;
            } else {
                $badges['badges'][$badge['badge_id']] = 1;
            }
        }

        unset($badge);

        $formattedBadges = [];
        foreach ($badges['badges'] as $badgeId => $badgeCount) {
            $formattedBadges['badges'][] = [
                "id" => $badgeId,
                "count" => $badgeCount
            ];
        }

        return $formattedBadges;
    }
}
