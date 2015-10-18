<?php
/*
Plugin Name: edf
Plugin URI: 
Description: Async form handler API and lib
Version: 0.1
Author: Steven Kornblum
*/


// Include WP MVC Lib
include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/wp-db.php');


/**
 * @desc edf lib
 * @author SDK (steve@eardish.com)
 * @date 2015-03-17
 */
class edf {


  /**
   * @desc Submit Fan Form
   * @author SDK (steve@eardish.com)
   * @date 2015-03-17
   * @param str $firstName - First name of applicant
   * @param str $lastName - Last name of applicant
   * @param str $email - Email address of applicant
   * @param str $dob - Date of birth of applicant
   * @param str $city - City of applicant
   * @param str $state - State (geographically) of applicant
   * @param str $zip - Zip code of applicant
   * @param str $gender - Gender of applicant (expect 'm' or 'f')
   * @param bool $pp - Accepted privacy policy? (true or false)
   * @param bool $tos - Accepted terms of service / terms and conditions? (true or false)
   * @return str - Return response (success or failure)
  */
  public static function fan($firstName, $lastName, $email, $dob, $city, $state, $zip, $gender, $pp, $tos) {
    global $wpdb;

    // If not provided necessary args, throw an error
    try {
      if(!isset($firstName) || !isset($lastName) || !isset($email) || !isset($dob) || !isset($city) || !isset($state) || !isset($zip) || !isset($gender) || !isset($pp) || !isset($tos)) {
        throw new Exception('This form submission was not complete.');
      }
    } catch(Exception $e) {
      return $e->getMessage();
    }

    // Process DOB
    $dob = date('Y-m-d', strtotime($dob));

    // Run insert query
    $sql = "INSERT INTO ed_fans SET
            first_name  = %s,
            last_name   = %s,
            email       = %s,
            dob         = %s,
            city        = %s,
            state       = %s,
            zip         = %s,
            gender      = %s,
            pp          = %s,
            tos         = %s,
            created     = NOW()";
    $res = $wpdb->query($wpdb->prepare($sql, $firstName, $lastName, $email, $dob, $city, $state, $zip, $gender, $pp, $tos));

    // Return result
    return ($res) ? 1 : 0;
  } // end function fan


  /**
   * @desc Submit Artist Form
   * @author SDK (steve@eardish.com)
   * @date 2015-03-17
   * @param str $artistType - Solo or Group?
   * @param str $artistName - Name of band or performer
   * @param str $genre - Genre of the artist
   * @param str $firstName - First name of applicant
   * @param str $lastName - Last name of applicant
   * @param str $address - Address of the contact
   * @param str $address2 - Line 2 of the contact's address [OPTIONAL]
   * @param str $city - City of applicant
   * @param str $state - State (geographically) of applicant
   * @param str $zip - Zip code of applicant
   * @param str $email - Email address of applicant
   * @param str $website - Website of applicant
   * @param str $twitter - Twitter handle of applicant
   * @param str $facebook - Facebook username of applicant
   * @param str $phone - Phone number of the contact
   * @param str $relationship - Contact's relationship to artist (e.g. artist, band member, parent, manager, agent, etc.)
   * @param str $referral - Referred to Eardish by? (e.g. scout, rep, etc.)
   * @param bool $pp - Accepted privacy policy? (true or false)
   * @param bool $tos - Accepted terms of service / terms and conditions? (true or false)
   * @return str - Return response (success or failure)
  */
  public static function artist($artistType, $artistName, $genre, $firstName, $lastName, $address, $address2, $city, $state, $zip, $email, $website, $twitter, $facebook, $phone, $relationship, $referral, $pp, $tos) {
    global $wpdb;

    // If not provided necessary args, throw an error
    try {
      if(!isset($artistType) || !isset($artistName) || !isset($genre) || !isset($firstName) || !isset($email) || !isset($phone)) {
        throw new Exception(0);
      }
    } catch(Exception $e) {
      return $e->getMessage();
    }

    // Run insert query
    $sql = "INSERT INTO ed_artists SET
            artist_type   = %s,
            artist_name   = %s,
            genre         = %s,
            first_name    = %s,
            last_name     = %s,
            address       = %s,
            address2      = %s,
            city          = %s,
            state         = %s,
            zip           = %s,
            email         = %s,
            website       = %s,
            twitter       = %s,
            facebook      = %s,
            phone         = %s,
            relationship  = %s,
            referral      = %s,
            pp            = %s,
            tos           = %s,
            created       = NOW()";
    $res = $wpdb->query($wpdb->prepare($sql, $artistType, $artistName, $genre, $firstName, $lastName, $address, $address2, $city, $state, $zip, $email, $website, $facebook, $twitter, $phone, $relationship, $referral, $pp, $tos));

    // Email notification
    if($res) {
      $message = "type: $artistType\r\n
artist: $artistName\r\n
genre: $genre\r\n
first name: $firstName\r\n
last name: $lastName\r\n
address: $address\r\n
address2: $address2\r\n
city: $city\r\n
state: $state\r\n
zip: $zip\r\n
email: $email\r\n
website: $website\r\n
facebook: $facebook\r\n
twitter: $twitter\r\n
phone: $phone\r\n
relationship: $relationship,
referral: $referral";
      @mail("sdk@eardish.com", "Eardish - New Founding Artist Application", $message);
    }

    // Return result
    return ($res) ? 1 : 0;
  } // end function artist


} // end class edf

