<?php

// Include WP MVC Lib
include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/wp-db.php');

// Include EDF Lib
include_once($_SERVER['DOCUMENT_ROOT'].'/wp-content/plugins/edf/edf.php');

// If AJAX post is made, process request/params and return JSON response
if(isset($_POST)) {

  // Check for post vars
  $action       = (isset($_POST['action']))       ? $_POST['action']        : NULL;
  $address      = (isset($_POST['address']))      ? $_POST['address']       : NULL;
  $address2     = (isset($_POST['address2']))     ? $_POST['address2']      : NULL;
  $artistName   = (isset($_POST['artistName']))   ? $_POST['artistName']    : NULL;
  $city         = (isset($_POST['city']))         ? $_POST['city']          : NULL;
  $dob          = (isset($_POST['dob']))          ? $_POST['dob']           : NULL;
  $email        = (isset($_POST['email']))        ? $_POST['email']         : NULL;
  $website      = (isset($_POST['website']))      ? $_POST['website']       : NULL;
  $twitter      = (isset($_POST['twitter']))      ? $_POST['twitter']       : NULL;
  $facebook     = (isset($_POST['facebook']))     ? $_POST['facebook']      : NULL;
  $firstName    = (isset($_POST['firstName']))    ? $_POST['firstName']     : NULL;
  $gender       = (isset($_POST['gender']))       ? $_POST['gender']        : NULL;
  $genre        = (isset($_POST['genre']))        ? $_POST['genre']         : NULL;
  $lastName     = (isset($_POST['lastName']))     ? $_POST['lastName']      : NULL;
  $phone        = (isset($_POST['phone']))        ? $_POST['phone']         : NULL;
  $pp           = (isset($_POST['pp']))           ? $_POST['pp']            : NULL;
  $relationship = (isset($_POST['relationship'])) ? $_POST['relationship']  : NULL;
  $referral     = (isset($_POST['referral']))     ? $_POST['referral']      : NULL;
  $state        = (isset($_POST['state']))        ? $_POST['state']         : NULL;
  $tos          = (isset($_POST['tos']))          ? $_POST['tos']           : NULL;
  $artistType   = (isset($_POST['artistType']))   ? $_POST['artistType']    : NULL;
  $zip          = (isset($_POST['zip']))          ? $_POST['zip']           : NULL;

  // Clear out result set
  unset($edf);
 
  // Process request
  switch($action) {
    case 'fan':
      $edf = edf::fan($firstName, $lastName, $email, $dob, $city, $state, $zip, $gender, $pp, $tos);
      break;
    case 'artist':
      $edf = edf::artist($artistType, $artistName, $genre, $firstName, $lastName, $address, $address2, $city, $state, $zip, $email, $website, $twitter, $facebook, $phone, $relationship, $referral, $pp, $tos);
      break;
    default:
      exit;    
  }

  // Serialize the return data
  echo json_encode($edf);

} // end check for _POST

