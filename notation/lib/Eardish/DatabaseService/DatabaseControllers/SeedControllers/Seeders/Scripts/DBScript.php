<?php
namespace Eardish\DatabaseService\DatabaseControllers\SeedControllers\Seeders\Scripts;

require 'bootstrap.php';

use Eardish\DatabaseService\DatabaseControllers\SeedControllers\Seeders\PostgresSeeder;

class DBScript extends PostgresSeeder
{
    public function trackGenre()
    {
        $resource = $this->database->querySeed('select * from track;');
        if($resource) {
            $tracks = (pg_fetch_all($resource));

            foreach($tracks as $track) {
                $trackId = $track['id'];
                $trackProfileId = $track['profile_id'];
                $profileGenreEntry = $this->database->selectSeed('profile_genre', array('profile_id' => $trackProfileId));
                $genreId = $profileGenreEntry[0]['genre_id'];
                $date = new \DateTime();
                $date = $date->format('c');
                $currentTrackGenre = $this->database->selectSeed('track_genre', array('track_id' => $trackId));
                if (!$currentTrackGenre) {
                    $this->database->insertSeed('track_genre', array('track_id' => $trackId, 'genre_id' => $genreId, 'date_created' => $date));
                    echo "inserting track genre for track $trackId\n";
                } else if ($currentTrackGenre[0]['genre_id'] != $genreId){
                    $this->database->updateSeed('track_genre', array('genre_id' => $genreId, 'date_modified' => $date), array('track_id' => $trackId));
                    echo "updating track genre for track $trackId\n";
                } else {
                    echo "track genre already exists for track $trackId\n";
                }
            }
        }
    }

    public function splits3Paths()
    {
        $resource = $this->database->querySeed('select * from image;');
        if($resource) {
            $images = (pg_fetch_all($resource));

            foreach($images as $image) {
                $id = $image['id'];
                $url = $image['url'];
                $parts = explode("/", $url);
                for ($i = 0; $i < 5; $i++) {
                    unset($parts[$i]);
                }
                $relative_url = implode("/", $parts);
                echo "Relative url: $relative_url\n";
                $this->database->updateSeed('image', array('relative_url' => $relative_url), array('id' => $id));
            }
        }

        $resource = $this->database->querySeed('select * from audio;');
        if($resource) {
            $audios = (pg_fetch_all($resource));

            foreach($audios as $audio) {
                $id = $audio['id'];
                $url = str_replace("%2F", "/", $audio['url']);
                $parts = explode("/", $url);
                for ($i = 0; $i < 5; $i++) {
                    unset($parts[$i]);
                }
                $relative_url = implode("/", $parts);
                echo "Relative url: $relative_url\n";
                $this->database->updateSeed('audio', array('relative_url' => $relative_url), array('id' => $id));
            }
        }
    }
}