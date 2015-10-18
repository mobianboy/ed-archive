<?php
require_once('vendor/autoload.php');

$climate = new \League\CLImate\CLImate();

// To use an environmental variable token, set your github token in the terminal:
// > GITHUB_TOKEN=<your token>
// > export GITHUB_TOKEN
$githubToken        = getenv("GITHUB_TOKEN");
// OR
// $githubToken = "<Your Github Token>";
$githubOrganization = 'Eardish';

// the list of repo in your github organization you want to align
$repos = array(
    'ephect-api'                => false,
    'ephect-slc'                => false,
    'ephect-auth-service'       => false,
    'analytics-service'         => false,
    'recommendation-service'    => false,
    'user-service'              => false,
    'group-service'             => false,
    'song-ingestion-service'    => false,
    'image-processing-service'  => false,
    'music-service'             => false,
    'email-service'             => false,
    'db-orm-service'            => false,
    'ephect-service-base'       => false,
    'ephect-sandbox'            => false,
    'ephect-data-objects'       => false,
    'db-cron'                   => false,
    'scripts'                   => false,
    'artools-client'            => true
);

// set to true if you want to delete labels first before seeding new labels
$deleteOldLabels = false;
$modifyLabels = false;
$addLabels = true;

// Adds labels if flag is set, set names and colors
// will not allow duplicate labels to be made if label already exists
$labelsToAdd = array(
    'Focus: DTO'                    => 'fbca04',
    "Focus: architecture"           => "006b75",
    "Focus: documentation"          => "207de5",
    "Focus: integration"            => "e11d81",
    "Focus: security"               => "eb6420",
    "Notes: fragile"                => "e11d21",
    "Notes: question"               => "fbca04",
    "Status: blocked"               => "e11d21",
    "Status: cleared"               => "009800",
    "Status: duplicate"             => "222222",
    "Status: help wanted"           => "eb6420",
    "Status: invalid"               => "222222",
    "Status: unknown"               => "ffffff",
    "Status: wontfix"               => "222222",
    "Type: EPIC"                    => "663096",
    "Type: administrative"          => "fef2c0",
    "Type: bug"                     => "e11d21",
    "Type: code quality/refactor"   => "009800",
    "Type: deployment"              => "bfe5bf",
    "Type: quick fix"               => "0052cc",
    "Type: research"                => "bfd4f2",
    "Type: task"                    => "0052cc",
    "Type: testing"                 => "bfd4f2",
    "in progress"                   => "ededed",
    "in-review"                     => "ededed",
    "ready"                         => "ededed",
    "test"                          => "bfd4f2"
);

$deleteAll = true;
$labelsToDelete = array(
    'Focus: DTO'                    => true,
    "Focus: architecture"           => true,
    "Focus: documentation"          => true,
    "Focus: integration"            => true,
    "Focus: security"               => true,
    "Notes: fragile"                => true,
    "Notes: question"               => true,
    "Status: blocked"               => true,
    "Status: cleared"               => true,
);

// will not modify labels unless modify flag set to true
$labelsToModify = array( array(
    'Focus: DTO'                     => array( 'Focus: DTOOOOOO' => 'fbca04')
//    "Focus: architecture"           => array("Focus: architecture" => "006b75"),
//    "Focus: documentation"          => array("Focus: documentation" => "207de5"),
//    "Focus: integration"            => array("Focus: integration" => "e11d81"),
//    "Focus: security"               => array("Focus: security" => "eb6420"),
//    "Notes: fragile"                => array("Notes: fragile" => "e11d21"),
//    "Notes: question"               => array("Notes: question" => "fbca04"),
//    "Status: blocked"               => array("Status: blocked" => "e11d21"),
//    "Status: cleared"               => array("Status: cleared" => "009800"),
//    "Status: duplicate"             => array("Status: duplicate" => "222222"),
//    "Status: help wanted"           => array("Status: help wanted" => "eb6420"),
//    "Status: invalid"               => array("Status: invalid" => "222222"),
//    "Status: unknown"               => array("Status: unknown" => "ffffff"),
//    "Status: wontfix"               => array("Status: wontfix" => "222222"),
//    "Type: EPIC"                    => array("Type: EPIC" => "663096"),
//    "Type: administrative"          => array("Type: administrative" => "fef2c0"),
//    "Type: bug"                     => array("Type: bug" => "e11d21"),
//    "Type: code quality/refactor"   => array("Type: code quality/refactor" => "009800"),
//    "Type: deployment"              => array("Type: deployment" => "bfe5bf"),
//    "Type: quick fix"               => array("Type: quick fix" => "0052cc"),
//    "Type: research"                => array("Type: research" => "bfd4f2"),
//    "Type: task"                    => array("Type: task" => "0052cc"),
//    "Type: testing"                 => array("Type: testing" => "bfd4f2"),
//    "in progress"                   => array("in progress" => "ededed"),
//    "in-review"                     => array("in-review" => "ededed"),
//    "ready"                         => array("ready" => "ededed")
));

$userConfirmationData = array();

//Delete old labels first if delete flag is on
if ($deleteOldLabels == true) {
    foreach($repos as $repo => $set) {
        if( $set === true ) {
            if ($deleteAll === true) {
                //get existing labels in a repo
                $labels = shell_exec( 'curl -u ' . $githubToken . ':x-oauth-basic https://api.github.com/repos/' . $githubOrganization . '/' . $repo . '/labels -X GET');
                if ($labels) {
                    print "got here";
                    // do a whole bunch of string parsing to get the name of the label
                    $existingLabels = explode("{",$labels);
                    array_shift($existingLabels);
                    foreach($existingLabels as $label) {
                        // more string parsing to format label name
                        // turn into array
                        $labelFields = explode(",",$label);
                        $nameField = ltrim($labelFields[1]);
                        //chop off "name" prefix
                        $name = substr($nameField, 8);
                        // get rid of extra quotes surrounding name
                        $name = substr($name, 1, -1);

                        $userConfirmationData[$name] = "";
                    }
                    if (userInput($userConfirmationData, $repo, "delete", $climate)) {
                        foreach ($userConfirmationData as $name => $color)
                        {
                            $name = urlencode($name);
                            //php urlencode does not match github url format, so need to modify to make names match
                            $name = str_replace("+", "%20", $name);

                            system( 'curl -u ' . $githubToken . ':x-oauth-basic https://api.github.com/repos/' . $githubOrganization . '/' . $repo . '/labels/' . $name . ' -X DELETE');
                        }
                    }
                }
            } else {
                foreach ($labelsToDelete as $label => $delete) {
                    if ($delete === true) {
                        $userConfirmationData[$label] = "";
                    }
                }
                if (userInput($userConfirmationData, $repo, "delete", $climate)) {
                    foreach ($userConfirmationData as $name => $color)
                    {
                        $name = urlencode($name);
                        //php urlencode does not match github url format, so need to modify to make names match
                        $name = str_replace("+", "%20", $name);

                        system( 'curl -u ' . $githubToken . ':x-oauth-basic https://api.github.com/repos/' . $githubOrganization . '/' . $repo . '/labels/' . $name . ' -X DELETE');
                    }
                }
            }
        }
    }
}


//modify
if ($modifyLabels == true) {
    foreach ($repos as $repo => $set) {
        if( $set === true ) {
            foreach ($labelsToModify as $oldLabelName) {
                foreach ($oldLabelName as $oldName => $newData) {
                    foreach ($newData as $newName => $newColor) {
                        $userConfirmationData[$newName] = $newColor;
                    }
                }
                if (userInput($userConfirmationData, $repo, "modify", $climate)) {
                    foreach ($userConfirmationData as $newName => $newColor)
                    {
                        $labelToSet = '\'{ "name": "' . $newName . '","color": "' . $newColor . '" }\'';

                        $oldName = urlencode($oldName);
                        //php urlencode does not match github url format, so need to modify to make names match
                        $oldName = str_replace("+", "%20", $oldName);

                        system('curl -u ' . $githubToken . ':x-oauth-basic https://api.github.com/repos/' . $githubOrganization . '/' . $repo . '/labels/' . $oldName . ' -X PATCH --data ' . $labelToSet);
                    }
                }
            }
        }
    }
}

//add
if ($addLabels == true) {
    foreach ($repos as $repo => $set) {
        if( $set === true ) {
            foreach ($labelsToAdd as $name => $color) {
                $userConfirmationData[$name] = $color;
            }
            if (userInput($userConfirmationData, $repo, "add", $climate)) {
                foreach ($userConfirmationData as $name => $color)
                {
                    $labelToSet = '\'{ "name": "' . $name . '","color": "' . $color . '" }\'';
                    var_dump($labelToSet);
                    system('curl -u ' . $githubToken . ':x-oauth-basic https://api.github.com/repos/' . $githubOrganization . '/' . $repo . '/labels -X POST --data ' . $labelToSet);
                }
            }

        }
    }
}

/**
 * @param $repoData
 * @param $repo
 * @param $action
 * @param $climate \League\CLImate\CLImate
 * @return bool
 */
function userInput($repoData, $repo, $action, $climate)
{
    echo "\nYou are about to $action the following labels in '$repo' repository:\n\n";

    foreach ($repoData as $name => $color) {
        if ($color != "") {
            echo "name: $name, color: $color\n";
        } else {
            echo "name: $name\n";
        }
    }

    $input = $climate->input("\nAre you sure you want to continue? y/n\n");
    $input->accept(['y', 'n']);

    $response = $input->prompt();

    if($response == 'y'){
        echo "Great! Lets go ahead\n";
        return true;
    } else {
        echo "Okay, will not $action labels in the '$repo' repository.\n";
        return false;
    }
}