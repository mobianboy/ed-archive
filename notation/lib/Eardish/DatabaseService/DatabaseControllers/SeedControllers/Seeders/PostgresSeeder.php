<?php
namespace Eardish\DatabaseService\DatabaseControllers\SeedControllers\Seeders;

require 'bootstrap.php';

use Eardish\DatabaseService\CronConnection;
use Eardish\DatabaseService\DatabaseControllers\SeedControllers\PostgresSeedController;
use Faker\Factory;

class PostgresSeeder
{

    const GENRE_COUNT = 6;

    protected $database;
    protected $faker;

    protected $acting_as_ids;
    protected $bandIds;
    protected $bands1 = array();
    protected $bands2 = array();
    protected $genres = array();
    protected $profiles = array();
    protected $users = array();
    protected $groups = array();
    protected $userGroups = array();
    protected $relatedGroups = array();
    protected $albums = array();
    protected $groupProfiles = array();
    protected $userProfiles = array();
    protected $userAlbums = array();
    protected $userTracks = array();
    protected $tracks = array();
    protected $trackPlays = array();
    protected $trackGenre = array();
    protected $trackRating = array();
    protected $playlists = array();
    protected $playlistFollowers = array();
    protected $playlistTracks = array();
    protected $friends = array();
    protected $albumTracks = array();
    protected $inviteCodes = array();
//    protected $resetPassCodes = array();
    protected $userGenreBlend = array();
    protected $userBadge = array();
    protected $profileGenreBlend = array();
    protected $profileBadge = array();
    protected $fanBadges = array();
    protected $artistBadges = array();
    protected $badge = array();
    protected $badges = array();
    protected $analytic = array();
    protected $analytics = array();
    protected $types = array(1,2,3);
    protected $bandMembers = array();
    protected $profileAlbums = array();
    protected $profileTracks = array();

    protected $arts = array();
    protected $images = array();
    protected $audio = array();
    protected $profileGenres = array();
    protected $contacts = array();
    protected $arReps = array();

    /**
     * @param $numElements
     * @param $config \Eardish\AppConfig
     */
    public function __construct($numElements, $config, $secretConfig)
    {


        $address = $config->get('notation.databases.postgre.address');
        $port = $config->get('notation.databases.postgre.port');
        $username = $secretConfig->get('notation.databases.postgre.username');
        $password = $secretConfig->get('notation.databases.postgre.password');

        $this->database = new PostgresSeedController(
            new CronConnection(
                $address,
                $port,
                $username,
                $password
            ),
            $address,
            $port,
            $username,
            $password
        );
        $this->numElements = $numElements;
        $this->faker = Factory::create("eardish");
        $this->faker->seed(1234);
    }

    public function seed()
    {
        $this->user();
        $this->badge();
        $this->genre();
        $this->contact();
        $this->setUpSongs();
        $this->fanProfile();
       // $this->art();
        $this->album();
      //  $this->track();
      //  $this->audio();
      //  $this->image();
        $this->playlist();
        $this->friend();
//        $this->inviteCodes();
        $this->profileAlbum();
        $this->profileTrack();
        $this->profileBadge();
        $this->profileGenreBlend();
        $this->trackGenre();
        $this->trackPlay();
        $this->trackRating();
        $this->playlistFollower();
        $this->playlistTrack();
        $this->albumTracks();
        $this->analytic();
        $this->profileGenre();
        $this->arRep();
        $this->bandMember();
    }

    public function user()
    {
        for ($i = 0; $i < $this->numElements; $i++) {

            $password = $this->faker->password;

            $password = str_replace('"', ".", $password);
            $password = str_replace('\'', ".", $password);
            $password = str_replace('\\', ".", $password);
            $this->users[] = array(
                "email" => $this->faker->email,
                "password" => $password,
                "deleted" => $this->faker->boolean(),
                "date_created" => $this->faker->dateTimeThisYear()->format('c'),
                "date_modified" => $this->faker->dateTimeThisMonth()->format('c')
            );
        }

        foreach ($this->users as $user) {
            $this->database->insertSeed('user', $user);
        }
    }

    public function bandMember()
    {
        for ($i = 0; $i < $this->numElements; $i++) {
            $this->bandMembers[] = array (
                "group_id" => intval($this->faker->randomElement($this->bands2)),
                "member_id" => intval($this->faker->randomElement($this->bands1)),
                "role" => $this->faker->word,
                "admin" => $this->faker->boolean(),
                "date_created" => $this->faker->dateTimeThisYear()->format('c'),
                "date_modified" => $this->faker->dateTimeThisMonth()->format('c')
            );
        }

        foreach ($this->bandMembers as $bandMember) {
            $this->database->insertSeed('band_member', $bandMember);
        }
    }

    public function badge()
    {
        $this->fanBadges = [
            [
                'name' => 'Iron Ears',
                'description' => 'completed-listens-fan',
                'type' => 'chart',
                'awarded_to' => 'fan'
            ],
            [
                'name' => 'Star Lord',
                'description' => 'most-tracks-rated-fan',
                'type' => 'chart',
                'awarded_to' => 'fan'
            ],
        ];

        $this->artistBadges = [
            [
                'name' => 'Most Completed Plays',
                'description' => 'completed-listens-track',
                'type' => 'chart',
                'awarded_to' => 'artist'
            ],
            [
                'name' => 'Highest Rated Song',
                'description' => 'highest-rated-track',
                'type' => 'chart',
                'awarded_to' => 'artist'
            ]
        ];

        $this->badges = array_merge($this->fanBadges, $this->artistBadges);

        foreach ($this->badges as $index => $badge) {
            $this->badge[] = array(
                "name" => $badge['name'],
                "description" => $badge['description'],
                "type" => $badge['type'],
                "awarded_to" => $badge['awarded_to'],
                "date_created" => $this->faker->dateTimeThisYear()->format('c'),
                "date_modified" => $this->faker->dateTimeThisMonth()->format('c')
            );
        }

        foreach ($this->badge as $badge) {
            $this->database->insertSeed('badge', $badge);
        }

    }

    public function profileAlbum()
    {
        for ($i = 0; $i < $this->numElements; $i++) {
            $this->profileAlbums[] = array(
                "profile_id" => rand(1, count($this->profiles)),
                "album_id" => rand(1, count($this->albums)),
                "date_created" => $this->faker->iso8601
            );
        }

        foreach ($this->profileAlbums as $profAlbums) {
            $this->database->insertSeed('profile_album', $profAlbums);
        }
    }

    public function profileTrack()
    {
        for ($i = 0; $i < $this->numElements; $i++) {
            $this->profileTracks[] = array(
                "profile_id" => $this->faker->randomElement($this->bandIds),
                "track_id" => rand(1, count($this->tracks)),
                "date_created" => $this->faker->dateTimeThisYear()->format('c'),
                "date_modified" => $this->faker->dateTimeThisMonth()->format('c')
            );
        }

        foreach ($this->profileTracks as $profTrack) {
            $this->database->insertSeed('profile_track', $profTrack);
        }
    }

    public function contact()
    {
        for ($i = 0; $i < $this->numElements; $i++) {
            $this->contacts[] = array(
                "phone" => $this->faker->phoneNumber,
                "address1" => $this->faker->streetAddress,
                "address2" => $this->faker->streetAddress,
                "city" => $this->faker->city,
                "state" => $this->faker->state,
                "zipcode" => $this->faker->postcode,
                "date_created" => $this->faker->dateTimeThisYear()->format('c'),
                "date_modified" => $this->faker->dateTimeThisMonth()->format('c')
            );
        }

        foreach ($this->contacts as $contact) {
            $this->database->insertSeed('contact', $contact);
        }
    }

    public function profileGenre()
    {
        for ($i = 0; $i < $this->numElements; $i++) {
            $this->profileGenres[] = array(
                "profile_id" => $this->faker->randomElement($this->bandIds),
                "genre_id" => rand (1, count($this->genres)),
                "date_created" => $this->faker->dateTimeThisYear()->format('c'),
                "date_modified" => $this->faker->dateTimeThisMonth()->format('c')
            );
        }

        foreach ($this->profileGenres as $profileGenre) {
            $this->database->insertSeed('profile_genre', $profileGenre);
        }
    }

    public function arRep()
    {
        for ($i = 0; $i < $this->numElements; $i++) {
            if($i % 2) {
                $repId = rand(1, $this->numElements);
                $arRep = null;
            } else {
                $repId = null;
                $arRep = $this->faker->firstName . " " . $this->faker->lastName;
            }
            $this->arReps[] = array(
                "artist_id" => $this->faker->randomElement($this->bandIds),
                "rep_id" => $repId,
                "ar_rep" => $arRep,
                "date_created" => $this->faker->dateTimeThisYear()->format('c'),
                "date_modified" => $this->faker->dateTimeThisMonth()->format('c')
            );
        }

        foreach ($this->arReps as $arRep) {
            $this->database->insertSeed('ar_rep', $arRep);
        }
    }

    public function analytic()
    {
        $deviceType = array("celluar device", "desktop", "laptop", "tablet");
        $deviceMake = array("Apple", "Samsung", "Sony");
        $carrier = array("Sprint", "T-Mobile", "Verizon", "AT&T");
        $OS = array("Windows", "Mac", "Linux");
        $event_type = array("play", "pause", "scrub", "quit", "rate", "invite", "register", "login", "logout", "editProfile", "editDiscoverBlend", "routeRequest", "completedListen");
        $view_route = array("/artist/1922/discography", "/user/create", "/album/524", "/artist/54", "/user/playlist/242");

        $trackEvents = array("play", "pause", "scrub", "quit", "rate", "completedListen");


        for ($i = 0; $i < 3000; $i++) {
            $event = $this->faker->randomElement($event_type);

            if (in_array($event, $trackEvents)) {
                $trackId = rand(1, $this->numElements-1);
                $artistId = $this->database->selectSeed('track', array('id' => $trackId));
            } else {
                $trackId = null;
                $artistId = null;
            }

            $this->analytics[] = array (
                "user_id" => rand(1, $this->numElements),
                "profile_id" => rand(1, $this->numElements),
                "artist_id" => $artistId,
                "track_id" => $trackId,
                "device_type" => $this->faker->randomElement($deviceType),
                "device_make" => $this->faker->randomElement($deviceMake),
                "device_model" => $this->faker->lastName . rand(1,100),
                "device_carrier" => $this->faker->randomElement($carrier),
                "device_os" => $this->faker->randomElement($OS),
                "client_version" => $this->faker->randomFloat(),
                "latitude" => $this->faker->latitude,
                "longitude" => $this->faker->longitude,
                "time" => $this->faker->iso8601,
                "view_route" => $this->faker->randomElement($view_route),
                "track_timecode" => rand(1, 240),
                "session_duration" => rand(1, 1000),
                "event_type" => $event,
                "values" => null,
                "date_created" => $this->faker->dateTimeThisMonth()->format('c')
            );
        }

        foreach ($this->analytics as $analytics) {
            $this->database->insertSeed("analytic", $analytics);
        }
    }

    public function genre()
    {
        $genres = array("Pop", "Rock", "Alternative", "Country", "Hip-Hop/Urban");
        foreach ($genres as $genre) {
            $this->genres[] = array (
                "name" => $genre,
                "date_created" => $this->faker->dateTimeThisMonth()->format('c')
            );
        }

        foreach ($this->genres as $genre) {
            $this->database->insertSeed("genre", $genre);
        }
    }

    public function trackRating()
    {
        $profiles = $this->database->selectSeed('profile', array("type" => "fan"));



        for ($i = 0; $i < 1000; $i++) {
            $this->trackRating[] = array (
                "track_id" => rand (1, 99),
                "profile_id" => $profiles[rand(0, 99)]['id'],
                "rating" => rand(1, 5),
                "date_created" => $this->faker->dateTimeBetween('-1 weeks', 'now')->format('c'),
                "date_modified" => $this->faker->dateTimeBetween('-1 weeks', 'now')->format('c')
            );
        }

        foreach ($this->trackRating as $trackRating) {
            $this->database->insertSeed('track_rating', $trackRating);
        }
    }

    public function trackGenre()
    {

        for ($i = 0; $i < $this->numElements -1; $i++) {
            $this->trackGenre[] = array (
                "track_id" => $i+1,
                "genre_id" => rand (1, 5),
                "date_created" => $this->faker->dateTimeThisYear()->format('c'),
                "date_modified" => $this->faker->dateTimeThisMonth()->format('c')
            );
        }

        foreach ($this->trackGenre as $trackGenre) {
            $this->database->insertSeed('track_genre', $trackGenre);
        }

    }

    public function friend()
    {
        for ($i = 0; $i < $this->numElements; $i++) {

            //each time it runs, all values are put back to be selected from
            $profileRange = range(1, $this->numElements);
            $profile_id = $this->faker->randomElement($profileRange);
            //makes sure that group1_id will be unique from group2_id
            $key = array_search($profile_id, $profileRange);
            unset($profileRange[$key]);
            $friend_id = $this->faker->randomElement($profileRange);

            $this->friends[] = array(
                "profile1_id" => $profile_id,
                "profile2_id" => $friend_id
            );
        }

        foreach ($this->friends as $friend) {
            $this->database->insertSeed('friend', $friend);
        }
    }

    public function album()
    {
        $bands1 = $this->database->selectSeed('profile', array('type' => 'artist-solo'));
        $bands2 = $this->database->selectSeed('profile', array('type' => 'artist-group'));
        $this->bandIds = array();

        foreach($bands1 as $band) {
            array_push($this->bands1, $band['id']);
            array_push($this->bandIds, $band['id']);
        }

        foreach($bands2 as $band) {
            array_push($this->bands2, $band['id']);
            array_push($this->bandIds, $band['id']);
        }

        for ($i = 0; $i < $this->numElements; $i++) {
            $art = array(
                "profile_id" => $this->faker->randomElement($this->bandIds),
                "type" => "album-art",
                "title" => $this->faker->sentence(rand(1,6)),
                "description" => $this->faker->bs,
                "date_created" => $this->faker->dateTimeThisYear()->format('c'),
                "date_modified" => $this->faker->dateTimeThisMonth()->format('c'),
                "deleted" => $this->faker->boolean()
            );
            $this->database->insertSeed('art', $art);
        }

        $dbAlbumArt = $this->database->selectSeed("art", array("type" => "album-art"));

        $formats = array("original", "phone_small", "phone_large", "tablet_small", "tablet_large", "thumbnail_small", "thumbnail_large");

        for ($i = 0; $i < $this->numElements; $i++) {
            $albumArt = array_pop($dbAlbumArt);
            $artId = $albumArt['id'];
            $image = array(
                "art_id" => $artId,
                "format" => $this->faker->randomElement($formats),
                "url" => $this->faker->randomElement(array("http://eardish.dev.images.s3.amazonaws.com/devs/track-art/XX.jpg", "http://eardish.dev.images.s3.amazonaws.com/devs/track-art/Air.jpg", "http://eardish.dev.images.s3.amazonaws.com/devs/track-art/RadioHead.jpg")),
                "date_created" => $this->faker->dateTimeThisYear()->format('c'),
                "date_modified" => $this->faker->dateTimeThisMonth()->format('c')
            );
            $this->database->insertSeed('image', $image);
        }

        for ($i = 0; $i < $this->numElements; $i++) {
            $album = array(
                "profile_id" => $this->faker->randomElement($this->bandIds),
                "release_date" => $this->faker->iso8601,
                "name" => $this->faker->sentence(rand(1,4)),
                "various_artist" => $this->faker->boolean(20),
                "art_id" => rand(1, $this->numElements),
                "record_label" => $this->faker->bs,
                "date_created" => $this->faker->dateTimeThisYear()->format('c'),
                "date_modified" => $this->faker->dateTimeThisMonth()->format('c')
            );
            $this->database->insertSeed('album', $album);
        }
    }

    public function art()
    {
        $types = array('avatar', 'track-art', 'album-art', 'audio');
        for ($i = 0; $i < $this->numElements; $i++) {
            $this->arts[] =array(
                "profile_id" => $this->faker->randomElement($this->bandIds),
                "type" => $this->faker->randomElement($types),
                "title" => $this->faker->sentence(rand(1,6)),
                "description" => $this->faker->bs,
                "date_created" => $this->faker->dateTimeThisYear()->format('c'),
                "date_modified" => $this->faker->dateTimeThisMonth()->format('c'),
                "deleted" => $this->faker->boolean()
            );
        }

        foreach ($this->arts as $art) {
            $this->database->insertSeed('art', $art);
        }
    }

    public function image()
    {
        $formats = array("original", "phone_small", "phone_large", "tablet_small", "tablet_large", "thumbnail_small", "thumbnail_large");

        for ($i = 0; $i < $this->numElements; $i++) {
            $this->images[] =array(
                "art_id" => rand(1, count($this->arts)),
                "format" => $this->faker->randomElement($formats),
                "url" => $this->faker->randomElement(array("http://eardish.dev.images.s3.amazonaws.com/devs/track-art/XX.jpg", "http://eardish.dev.images.s3.amazonaws.com/devs/track-art/Air.jpg", "http://eardish.dev.images.s3.amazonaws.com/devs/track-art/RadioHead.jpg")),
                "date_created" => $this->faker->dateTimeThisYear()->format('c'),
                "date_modified" => $this->faker->dateTimeThisMonth()->format('c')
            );
        }

        foreach ($this->images as $image) {
            $this->database->insertSeed('image', $image);
        }
    }

    public function track()
    {
        for ($i = 0; $i < $this->numElements; $i++) {
            $this->tracks[] =array(
                "profile_id" => $this->faker->randomElement($this->bandIds),
                "art_id" => rand(1, count($this->arts)),
                "name" => $this->faker->sentence(rand(1,6)),
                "length" => $this->faker->randomFloat(2, 1.0, 7.0),
                "date_created" => $this->faker->dateTimeThisYear()->format('c'),
                "date_modified" => $this->faker->dateTimeThisMonth()->format('c'),
                "deleted" => $this->faker->boolean()
            );
        }

        foreach ($this->tracks as $track) {
            $this->database->insertSeed('track', $track);
        }
    }

    public function audio()
    {
        $formats = array("original");

        for ($i = 0; $i < $this->numElements; $i++) {
            $this->audio[] = array(
                "track_id" => rand(1, $this->numElements),
                "format" => $this->faker->randomElement($formats),
                "url" => $this->faker->randomElement(array("http://eardish.dev.songs.s3.amazonaws.com/devs/tracks/Crystalised.flac", "http://eardish.dev.songs.s3.amazonaws.com/devs/tracks/Remember.mp3")),
                "bitrate" => $this->faker->randomElement(array('128', '320')),
                "encoding" => $this->faker->randomElement(array('mp3', 'flac', 'wav')),
                "date_created" => $this->faker->dateTimeThisYear()->format('c'),
                "date_modified" => $this->faker->dateTimeThisMonth()->format('c')
            );
        }

        foreach ($this->audio as $audio) {
            $this->database->insertSeed('audio', $audio);
        }
    }

    public function fanProfile()
    {
        $avatars = array(
            array("title" => "headshot1", "url" => "http://eardish.dev.images.s3.amazonaws.com/devs/avatar/headshot1.jpg"),
            array("title" => "headshot2", "url" => "http://eardish.dev.images.s3.amazonaws.com/devs/avatar/headshot2.jpg"),
            array("title" => "headshot3", "url" => "http://eardish.dev.images.s3.amazonaws.com/devs/avatar/headshot3.jpg")
        );

        for ($j = 0; $j < count($avatars); $j++) {
            $avatar = $avatars[$j];

            for ($i = 0; $i < $this->numElements/3; $i++) {
                $profile = array(
                    "user_id" => rand(1, count($this->users)),
                    "art_id" => $j+1,
                    "contact_id" => rand(1, $this->numElements),
                    "type" => 'fan',
                    "first_name" => $this->faker->firstName,
                    "last_name" => $this->faker->lastName,
                    "year_of_birth" => $this->faker->iso8601,
                    "date_created" => $this->faker->dateTimeThisYear()->format('c'),
                    "date_modified" => $this->faker->dateTimeThisMonth()->format('c')
                );
                $this->database->insertSeed("profile", $profile);
            }

            $fanProfiles =  $dBProfiles = $this->database->selectSeed("profile", array("type" => "fan", "art_id" => $j+1));

            $art = array(
                "profile_id" => $this->faker->randomElement($fanProfiles)['id'],
                "type" => "avatar",
                "title" => $avatar['title'],
                "description" => $this->faker->bs,
                "date_created" => $this->faker->dateTimeThisYear()->format('c'),
                "date_modified" => $this->faker->dateTimeThisMonth()->format('c'),
                "deleted" => $this->faker->boolean()
            );
            $this->database->insertSeed("art", $art);


            $dBArts =  $dBProfiles = $this->database->selectSeed("art", array("title" => $avatar['title']));

            $artId = $this->faker->randomElement($dBArts)['id'];
            $image = array(
                "art_id" => $artId,
                "format" => "original",
                "url" => $avatar['url'],
                "date_created" => $this->faker->dateTimeThisYear()->format('c'),
                "date_modified" => $this->faker->dateTimeThisMonth()->format('c')
            );
            $this->database->insertSeed('image', $image);
            $this->database->updateSeed("profile", array("art_id" => $artId), array("art_id" => $j+1));
        }
    }

    public function setUpSongs()
    {
        $tracks = array(
                array(
                    "title" => "Crystalized",
                    "artist" => "The XX",
                    "avatar" => "http://eardish.dev.images.s3.amazonaws.com/devs/bandAvatar/thexx.jpg",
                    "bio" => "The xx are an English indie pop band formed in 2005 in Wandsworth, London.",
                    "image" => "http://eardish.dev.images.s3.amazonaws.com/devs/track-art/XX.jpg",
                    "artTitle" => "XX.jpg",
                    "audio" => "http://eardish.dev.songs.s3.amazonaws.com/devs/tracks/Crystalised.mp3"
                ),
                array(
                    "title" => "Remember",
                    "artist" => "Air",
                    "avatar" => "http://eardish.dev.images.s3.amazonaws.com/devs/bandAvatar/air.jpg",
                    "bio" => "Air is a music duo from Versailles, France, consisting of Nicolas Godin and Jean-Benoît Dunckel.",
                    "image" => "http://eardish.dev.images.s3.amazonaws.com/devs/track-art/Air.jpg",
                    "artTitle" => "Air.jpg",
                    "audio" => "http://eardish.dev.songs.s3.amazonaws.com/devs/tracks/Remember.mp3"
                ),
                array(
                    "title" => "Paranoid Android",
                    "artist" => "Radio Head",
                    "avatar" => "http://eardish.dev.images.s3.amazonaws.com/devs/bandAvatar/radiohead.jpg",
                    "bio" => "Radiohead are an English rock band from Abingdon, Oxfordshire, formed in 1985.",
                    "image" => "http://eardish.dev.images.s3.amazonaws.com/devs/track-art/RadioHead.jpg",
                    "artTitle" => "RadioHead.jpg",
                    "audio" => "http://eardish.dev.songs.s3.amazonaws.com/devs/tracks/ParanoidAndroid.mp3"
                )
        );

        for ($j = 0; $j < count($tracks); $j++) {
            $track = $tracks[$j];

            $inviteCode = $this->faker->word;

            for ($i = 0; $i < 33; $i++) {
                $profile = array(
                    "user_id" => rand(1, count($this->users)),
                    "art_id" => null,
                    "contact_id" => rand(1, $this->numElements),
                    "type" => $this->faker->randomElement(array("artist-group", "artist-solo")),
                    "invite_code" => $inviteCode,
                    "artist_name" => $track['artist'],
                    "bio" => $track['bio'],
                    "website" => $this->faker->url,
                    "year_founded" => $this->faker->year($max = 'now'),
                    "hometown" => $this->faker->city . ", " . $this->faker->state,
                    "facebook_page" => $this->faker->url,
                    "twitter_page" => $this->faker->url,
                    "date_created" => $this->faker->dateTimeThisYear()->format('c'),
                    "date_modified" => $this->faker->dateTimeThisMonth()->format('c')
                );
                $this->database->insertSeed('profile', $profile);
            }
            $dBProfiles = $this->database->selectSeed("profile", array("artist_name" => $track['artist']));

            // insert one of each profile Art
            $avatarArt = array(
                "profile_id" => $this->faker->randomElement($dBProfiles)['id'],
                "type" => 'avatar',
                "title" => $track['artist']. "Avatar",
                "description" => $this->faker->bs,
                "date_created" => $this->faker->dateTimeThisYear()->format('c'),
                "date_modified" => $this->faker->dateTimeThisMonth()->format('c'),
                "deleted" => $this->faker->boolean()
            );
            $this->database->insertSeed("art", $avatarArt);

            //insert track art
            $trackArt = array(
                "profile_id" => $this->faker->randomElement($dBProfiles)['id'],
                "type" => 'track-art',
                "title" => $track['artTitle'],
                "description" => $this->faker->bs,
                "date_created" => $this->faker->dateTimeThisYear()->format('c'),
                "date_modified" => $this->faker->dateTimeThisMonth()->format('c'),
                "deleted" => $this->faker->boolean()
            );
            $this->database->insertSeed('art', $trackArt);

            $dbTrackArts = $this->database->selectSeed("art", array("title" => $track['artTitle']));
            $dbAvatarArts = $this->database->selectSeed("art", array("title" => $track['artist']. "Avatar"));

            $trackArtId = array_pop($dbTrackArts)['id'];
            $image = array(
                "art_id" =>$trackArtId,
                "format" => "original",
                "url" => $track['image'],
                "date_created" => $this->faker->dateTimeThisYear()->format('c'),
                "date_modified" => $this->faker->dateTimeThisMonth()->format('c')
            );
            $this->database->insertSeed('image', $image);

            $avatarId = array_pop($dbAvatarArts)['id'];
            $avatar = array(
                "art_id" => $avatarId,
                "format" => "original",
                "url" => $track['image'],
                "date_created" => $this->faker->dateTimeThisYear()->format('c'),
                "date_modified" => $this->faker->dateTimeThisMonth()->format('c')
            );
            $this->database->insertSeed('image', $avatar);

            for ($i = 0; $i < 33; $i++) {
                $profileEntry = array_pop($dBProfiles);
                $profileId = $profileEntry['id'];
                $trackEntry = array(
                    "profile_id" => $profileId,
                    "art_id" => $trackArtId,
                    "name" => $track['title'],
                    "length" => rand(47, 600),
                    "date_created" => $this->faker->dateTimeThisYear()->format('c'),
                    "date_modified" => $this->faker->dateTimeThisMonth()->format('c'),
                    "deleted" => $this->faker->boolean()
                );
                $this->database->insertSeed('track', $trackEntry);
                //add art id to profile
                $this->database->updateSeed("profile", array("art_id" => $avatarId), array("artist_name" => $track['artist']));
            }

            $dBTracks = $this->database->selectSeed("track", array("name" =>  $track['title']));

            for ($i = 0; $i < 33 ; $i++) {
                $trackAudio = array_pop($dBTracks);
                $trackId = $trackAudio['id'];

                $audio = array(
                    "track_id" => $trackId,
                    "format" => "original",
                    "url" => $track['audio'],
                    "bitrate" => $this->faker->randomElement(array('128', '320')),
                    "encoding" => $this->faker->randomElement(array('mp3', 'flac', 'wav')),
                    "date_created" => $this->faker->dateTimeThisYear()->format('c'),
                    "date_modified" => $this->faker->dateTimeThisMonth()->format('c')
                );
                $this->database->insertSeed('audio', $audio);
            }
        }
    }


    public function profileGenreBlend()
    {
        $genreSize = count($this->genres);
        $weights = array(0,2,2,2);
        for ($i = 1; $i < $this->numElements; $i++) {
            $genres = range(1, $genreSize);
            shuffle($genres);
            foreach ($weights as $weight) {
                $genrePop = array_pop($genres);
                $this->database->insertSeed('profile_genre_blend', array(
                    "profile_id" => $i,
                    "genre_id" =>  $genrePop,
                    "weight" =>  $weight,
                    "date_created" => $this->faker->dateTimeThisYear()->format('c'),
                    "date_modified" => $this->faker->dateTimeThisMonth()->format('c')
                ));
            }
        }
    }

    public function profileBadge()
    {
        $fanBadgesSize = count($this->fanBadges);
        $artistBadgesSize = count($this->fanBadges);

        $fanProfiles = $this->database->selectSeed("profile", array("type" => "fan"));
        $artistSoloProfiles = $this->database->selectSeed("profile", array("type" => "artist-solo"));
        $artistGroupProfiles = $this->database->selectSeed("profile", array("type" => "artist-group"));

        $profiles = array_merge($fanProfiles, $artistGroupProfiles, $artistSoloProfiles);

        foreach($profiles as $index => $profile) {
            if ($profile['type'] == 'fan') {
                $badgeId = rand(1, $fanBadgesSize);
            } else {
                $badgeId = rand($fanBadgesSize + 1, $fanBadgesSize + $artistBadgesSize);
            }

            $this->profileBadge[] = array(
                "profile_id" => $profile['id'],
                "badge_id" =>  $badgeId,
                "date_created" => $this->faker->dateTimeThisYear()->format('c'),
                "date_modified" => $this->faker->dateTimeThisMonth()->format('c')
            );
        }

        foreach ($this->profileBadge as $profileBadge) {
            $this->database->insertSeed('profile_badge', $profileBadge);
        }
    }

    public function trackPlay()
    {

        for ($i = 0; $i < $this->numElements; $i++) {
            $this->trackPlays[] = array(
                "profile_id" =>  rand(1, count($this->profiles)-1),
                "track_id" => rand(1, count($this->tracks)),
                "date_created" => $this->faker->dateTimeThisMonth()->format('c')
            );
        }

        foreach ($this->trackPlays as $trackPlay) {
            $this->database->insertSeed('track_play', $trackPlay);
        }
    }

    public function playlist()
    {
        for ($i = 0; $i < $this->numElements; $i++) {
            $this->playlists[] = array(
                "profile_id" => rand(1, count($this->profiles)-1),
                "name" =>  $this->faker->sentence(rand(1,3)),
                "date_created" => $this->faker->dateTimeThisYear()->format('c'),
                "date_modified" => $this->faker->dateTimeThisMonth()->format('c')
            );
        }

        foreach ($this->playlists as $playlist) {
            $this->database->insertSeed('playlist', $playlist);
        }
    }

    public function playlistFollower()
    {
        for ($i = 0; $i < $this->numElements; $i++) {
            $this->playlistFollowers[] = array(
                "playlist_id" => rand(1, count($this->playlists)-1),
                "follower_id" => rand(1, count($this->profiles)-1),
                "date_created" => $this->faker->dateTimeThisYear()->format('c'),
                "date_modified" => $this->faker->dateTimeThisMonth()->format('c')
            );
        }

        foreach ($this->playlistFollowers as $playlistFollower) {
            $this->database->insertSeed('playlist_follower', $playlistFollower);
        }
    }

    public function playlistTrack()
    {
        for ($i = 0; $i < $this->numElements; $i++) {
            $this->playlistTracks[] = array(
                "playlist_id" => rand(1, count($this->playlists)-1),
                "track_id" => rand(1, count($this->tracks)-1),
                "track_position" => rand(1,20),
                "date_created" => $this->faker->dateTimeThisYear()->format('c'),
                "date_modified" => $this->faker->dateTimeThisMonth()->format('c')
            );
        }

        foreach ($this->playlistTracks as $playlistTrack) {
            $this->database->insertSeed('playlist_track', $playlistTrack);
        }
    }

    public function albumTracks()
    {
        for ($i = 0; $i < $this->numElements; $i++) {
            $this->albumTracks[] = array(
                "album_id" => rand(1, count($this->playlists)-1),
                "track_id" => rand(1, count($this->tracks)-1),
                "track_num" => rand(1,20),
                "date_created" => $this->faker->dateTimeThisYear()->format('c'),
                "date_modified" => $this->faker->dateTimeThisMonth()->format('c')
            );
        }

        foreach ($this->albumTracks as $albumTrack) {
            $this->database->insertSeed('album_track', $albumTrack);
        }
    }
//
//    public function inviteCodes()
//    {
//        $users = range(1, 100);
//        shuffle($users);
//        for ($i = 0; $i < $this->numElements; $i++) {
//            $user = array_pop($users);
//            $this->inviteCodes[] = array(
//                "inviter_id" => rand(1, count($this->users)-1),
//                "invitee_id" => $user,
//                "invite_code" => substr(md5(uniqid(mt_rand(), true)) , 0, 8),
//                "invitee_email" => "intdevuser@eardish.com",
//                "date_issued" => $this->faker->dateTimeThisYear()->format('c'),
//                "date_redeemed" => $this->faker->dateTimeThisMonth()->format('c')
//            );
//        }
//
//        foreach ($this->inviteCodes as $inviteCode) {
//            $this->database->insertSeed('invite', $inviteCode);
//        }
//    }

}