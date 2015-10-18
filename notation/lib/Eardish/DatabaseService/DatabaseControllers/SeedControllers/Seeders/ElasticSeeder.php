<?php
namespace Eardish\DatabaseService\DatabaseControllers\SeedControllers\Seeders;

use Eardish\DatabaseService\DatabaseControllers\SeedControllers\ElasticSeedController;
use Eardish\DatabaseService\DatabaseControllers\SeedControllers\Seeders\Core\PostgresData;


class ElasticSeeder extends \SeedData {

    protected $postgres;

    protected $users = array();
    protected $userAlbums = array();
    protected $userGroup = array();
    protected $userProfile = array();
    protected $userTrack = array();
    protected $groups = array();
    protected $groupProfiles = array();
    protected $albums = array();
    protected $albumTracks = array();
    protected $friends = array();
    protected $mediaArray = array();
    protected $playlists = array();
    protected $playlistFollowers = array();
    protected $playlistTracks = array();
    protected $tracks = array();
    protected $trackPlay = array();
    protected $inviteCode = array();

    public function __construct($numElements)
    {
        $this->numElements = $numElements;
        $this->database = new ElasticSeedController();
        $this->postgresData = new PostgresData($numElements);
    }

    public function seed()
    {
        $this->user();
        $this->userAlbum();
        $this->userGroup();
        $this->userProfile();
        $this->userTrack();
        $this->group();
        $this->groupProfile();
        $this->album();
        $this->albumTrack();
        $this->friend();
        $this->media();
        $this->playlist();
        $this->playlistFollower();
        $this->playlistTrack();
        $this->track();
        $this->trackPlay();
        $this->inviteCode();
    }

    public function user()
    {
        foreach($this->postgresData->getUsers() as $user) {
            $this->database->insertSeed("user", $user);
        }
    }

    public function userAlbum()
    {
        foreach($this->postgresData->getUserAlbums() as $userAlbum) {
            $this->database->insertSeed("userAlbum", $userAlbum);
        }
    }

    public function userGroup()
    {
        foreach($this->postgresData->getUserGroup() as $userGroup) {
            $this->database->insertSeed("userGroup", $userGroup);
        }
    }

    public function userProfile()
    {
        foreach($this->postgresData->getUserProfiles() as $userProfile) {
            $this->database->insertSeed("userProfile", $userProfile);
        }
    }

    public function userTrack()
    {
        foreach($this->postgresData->getUserTracks() as $userTrack) {
            $this->database->insertSeed("userTrack", $userTrack);
        }
    }

    public function group()
    {
        foreach($this->postgresData->getGroups() as $group) {
            $this->database->insertSeed("group", $group);
        }
    }

    public function groupProfile()
    {
        foreach($this->postgresData->getGroupProfiles() as $groupProfile) {
            $this->database->insertSeed("groupProfile", $groupProfile);
        }
    }

    public function album()
    {
        foreach($this->postgresData->getAlbums() as $album) {
            $this->database->insertSeed("album", $album);
        }
    }

    public function albumTrack()
    {
        foreach($this->postgresData->getAlbumTracks() as $albumTrack) {
            $this->database->insertSeed("albumTrack", $albumTrack);
        }
    }

    public function friend()
    {
        foreach($this->postgresData->getFriends() as $friend) {
            $this->database->insertSeed("friend", $friend);
        }
    }

    public function media()
    {
        foreach($this->postgresData->getMediaArray() as $media) {
            $this->database->insertSeed("media", $media);
        }
    }

    public function playlist()
    {
        foreach($this->postgresData->getPlaylists() as $playlist) {
            $this->database->insertSeed("playlist", $playlist);
        }
    }

    public function playlistFollower()
    {
        foreach($this->postgresData->getPlaylistFollowers() as $playlistFollower) {
            $this->database->insertSeed("playlistFollower", $playlistFollower);
        }
    }

    public function playlistTrack()
    {
        foreach($this->postgresData->getPlaylistTracks() as $playlistTrack) {
            $this->database->insertSeed("playlistTrack", $playlistTrack);
        }
    }

    public function track()
    {
        foreach($this->postgresData->getTracks() as $track) {
            $this->database->insertSeed("track", $track);
        }
    }

    public function trackPlay()
    {
        foreach($this->postgresData->getTrackPlays() as $trackPlay) {
            $this->database->insertSeed("trackPlay", $trackPlay);
        }
    }

    public function inviteCode()
    {
        foreach($this->postgresData->getInviteCodes() as $inviteCode) {
            $this->database->insertSeed("inviteCode", $inviteCode);
        }
    }
}