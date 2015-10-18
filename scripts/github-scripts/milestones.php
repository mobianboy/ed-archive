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
    'ephect-api'                => true,
    'ephect-slc'                => true,
    'ephect-auth-service'       => true,
    'analytics-service'         => true,
    'recommendation-service'    => true,
    'user-service'              => true,
    'group-service'             => true,
    'song-ingestion-service'    => true,
    'image-processing-service'  => true,
    'music-service'             => true,
    'email-service'             => true,
    'db-orm-service'            => true,
    'ephect-service-base'       => true,
    'ephect-sandbox'            => true,
    'ephect-data-objects'       => true,
    'db-cron'                   => true,
    'scripts'                   => true,
    'artools-client'            => true
);

$milestonesToAdd = array(
    [
        "title" => "Sprint 12",
        "due_on" => "2015-04-01T12:00:00Z"
    ]
);


//add
foreach ($repos as $repo => $set) {
    if( $set === true ) {
        foreach ($milestonesToAdd as $milestone) {
            foreach ($milestone as $column => $value) {
                $userConfirmationData[$column] = $value;
            }
            if (userInput($userConfirmationData, $repo, "add", $climate)) {
                $milestoneToSet = '\'{ ';
                $i = 0;
                $count = count($userConfirmationData);
                foreach ($userConfirmationData as $column => $value)
                {
                    $milestoneToSet .= '"'.$column.'": ' . '"'.$value.'"';
                    if ($i < $count -1) {
                        $milestoneToSet .= ",";
                    } else {
                        $milestoneToSet .= ' }\'';
                        continue;
                    }
                    $i++;
                }
                system('curl -u ' . $githubToken . ':x-oauth-basic https://api.github.com/repos/' . $githubOrganization . '/' . $repo . '/milestones -X POST --data ' . $milestoneToSet);
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
    echo "\nYou are about to $action the following milestone in '$repo' repository:\n\n";

    foreach ($repoData as $column => $value) {
        echo "$column: $value\n";
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