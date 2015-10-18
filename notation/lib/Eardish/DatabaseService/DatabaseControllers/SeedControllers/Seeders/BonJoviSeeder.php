<?php
/**
 * Created by PhpStorm.
 * User: darias
 * Date: 2/17/15
 * Time: 2:48 PM
 */

namespace Eardish\DatabaseService\DatabaseControllers\SeedControllers\Seeders;

//Add the example data for the artist Bon Jovi
class BonJoviSeeder extends PostgresSeeder {

    protected $bonJoviTracks = array();

    public function seed()
    {
        $this->user();
        $this->userProfile();
        $this->group();
        $this->groupProfile();
        $this->userGroup();
        $this->album();
        $this->track();
        $this->media();
        $this->albumTracks();
    }

    public function user()
    {
        $hashPass = $this->generatePassHash("123456");

        $user = array(
            "id" => 1284,
            "username" => 'Bonjovi',
            "email" => $this->faker->email,
            "password" => password_hash("123456", PASSWORD_DEFAULT, array("cost" => 11)),
            "password_confirmation" => password_hash("123456", PASSWORD_DEFAULT, array("cost" => 11)),
            "deleted" => false
        );

        $this->database->insertSeed('user', $user);

    }

    public function userProfile()
    {

        $userProfile = array(
            "user_id" => 1284,
            "first_name" => 'Jon',
            "last_name" => 'Jovi',
            "location" => 'Perth Amboy, New Jersey',
            "bio" => 'John Francis Bongiovi, Jr. (born March 2, 1962), known as Jon Bon Jovi, is an American singer-songwriter, record producer,
                    philanthropist, and actor, best known as the founder and frontman of rock band Bon Jovi, which was formed in 1983.',
            "default" =>false
        );


        $this->database->insertSeed('user_profile', $userProfile);

    }


    public function group()
    {
        $date = new \DateTime("NOW");

        $group = array(
            'id' => 1922,
            "type" => 1, //Either 1,2, or 3
            "created_by_id" => 1284,
            "created_date" => $date->format('c'),
            "deleted" => false
        );


        $this->database->insertSeed('group', $group);
    }

    public function groupProfile()
    {

        $groupProfile = array(
            "group_id" => 1922,
            "name" => "Bon Jovi",
            "location" => "Sayreville, New Jersey",
            "genre" => "rock",
            "year_founded" => "1983",
            "bio" => "",
            "website" => "",
            "deleted" => false,
            "default" => true
        );



        $this->database->insertSeed('group_profile', $groupProfile);

    }

    public function userGroup()
    {
        $userGroup = array(
            "user_id" => 1284,
            "group_id" =>  1922,
            "acting_as_id" => null,
            "admin" => true,
            "band_member" => true,
            "role" => "vocalist"
        );



        $this->database->insertSeed('user_group', $userGroup);

    }

    public function album()
    {
        $date = new \DateTime("NOW");

        $album = array(
            "id" => 524,
            "group_id" => 1922,
            "release_date" => $date->format("c"),
            "name" => 'Bon Jovi',
            "various_artist" => false,
            "album_art" => $this->faker->imageUrl,
            "record_label" => 'Mercury Records'
        );


        $this->database->insertSeed('album', $album);

    }

    public function track()
    {
        $this->fillTracksArray();

        foreach ($this->bonJoviTracks as $track) {
            $this->database->insertSeed('track', $track);
        }
    }

    public function media()
    {
        $quality = array("128", "320", "original", "lossless");

        for ($i = 0; $i < $this->numElements; $i++) {
            $this->media[] =array(
                "track_id" => rand(1, count($this->tracks)),
                "quality" => $this->faker->randomElement($quality),
                "location" => $this->faker->url
            );
        }

        foreach ($this->media as $media) {
            $this->database->insertSeed('media', $media);
        }
    }


    public function albumTracks()
    {
        foreach($this->bonJoviTracks as $trackNum => $track) {
            $this->albumTracks[] = array(
                "album_id" => 524,
                "track_id" => $track['id'],
                "track_num" => ++$trackNum
            );
        }

        foreach ($this->albumTracks as $albumTrack) {
            $this->database->insertSeed('album_track', $albumTrack);
        }
    }

    public function fillTracksArray()
    {
        $this->bonJoviTracks = array(
            array (                                                 //Track 1
                "id" => 500,
                "group_id" => 1922,
                "name" => 'Runaway',
                "length" => 3.83,
                "play_count" => rand(1000, 19204),
                "waveform_image_loc" => $this->faker->imageUrl,
                "create_date" => $this->faker->iso8601,
                "deleted" => false
            ), array (                                              //Track 2
                "id" => 501,
                "group_id" => 1922,
                "name" => 'Roulette',
                "length" => 4.68,
                "play_count" => rand(1000, 19204),
                "waveform_image_loc" => $this->faker->imageUrl,
                "create_date" => $this->faker->iso8601,
                "deleted" => false
            ), array (                                              //Track 3
                "id" => 502,
                "group_id" => 1922,
                "name" => "She Don't Know Me",
                "length" => 4.05,
                "play_count" => rand(1000, 19204),
                "waveform_image_loc" => $this->faker->imageUrl,
                "create_date" => $this->faker->iso8601,
                "deleted" => false
            ), array (                                              //Track 4
                "id" => 503,
                "group_id" => 1922,
                "name" => 'Shot Through The Heart',
                "length" => 4.42,
                "play_count" => rand(1000, 19204),
                "waveform_image_loc" => $this->faker->imageUrl,
                "create_date" => $this->faker->iso8601,
                "deleted" => false
            ), array (                                              //Track 5
                "id" => 504,
                "group_id" => 1922,
                "name" => 'Love Lies',
                "length" => 4.18,
                "play_count" => rand(1000, 19204),
                "waveform_image_loc" => $this->faker->imageUrl,
                "create_date" => $this->faker->iso8601,
                "deleted" => false
            ), array (                                              //Track 6
                "id" => 505,
                "group_id" => 1922,
                "name" => 'Breakout',
                "length" => 5.35,
                "play_count" => rand(1000, 19204),
                "waveform_image_loc" => $this->faker->imageUrl,
                "create_date" => $this->faker->iso8601,
                "deleted" => false
            ), array (                                              //Track 7
                "id" => 506,
                "group_id" => 1922,
                "name" => 'Burning for Love',
                "length" =>3.90,
                "play_count" => rand(1000, 19204),
                "waveform_image_loc" => $this->faker->imageUrl,
                "create_date" => $this->faker->iso8601,
                "deleted" => false
            ), array (                                              //Track 8
                "id" => 507,
                "group_id" => 1922,
                "name" => 'Come Back',
                "length" =>3.99,
                "play_count" => rand(1000, 19204),
                "waveform_image_loc" => $this->faker->imageUrl,
                "create_date" => $this->faker->iso8601,
                "deleted" => false
            ), array (                                              //Track 9
                "id" => 508,
                "group_id" => 1922,
                "name" => 'Get Ready',
                "length" => 4.16,
                "play_count" => rand(1000, 19204),
                "waveform_image_loc" => $this->faker->imageUrl,
                "create_date" => $this->faker->iso8601,
                "deleted" => false
            )
        );
    }

    public function generatePassHash($password, $options = array('cost' => 11))
    {
        $hashedPass = password_hash($password, PASSWORD_DEFAULT, $options);
        return $hashedPass;
    }
}