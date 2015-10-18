<?php
namespace Eardish\DatabaseService\DatabaseControllers\SeedControllers\Seeders\Core;

use Eardish\DatabaseService\CronConnection;
use Eardish\DatabaseService\DatabaseControllers\SeedControllers\PostgresSeedController;

class PostgresData
{
    /**
     * @var PostgresSeedController
     */
    protected $postgres;
    protected $numElements;

    protected $users = array();
    protected $userProfiles = array();
    protected $friends = array();
    protected $albums = array();
    protected $userAlbums = array();
    protected $groups = array();
    protected $tracks = array();
    protected $groupProfiles = array();
    protected $relatedGroups = array();
    protected $userTracks = array();
    protected $playlists = array();
    protected $playlistFollowers = array();
    protected $playlistTracks = array();
    protected $mediaArray = array();
    protected $userGroups = array();
    protected $trackPlays = array();
    protected $albumTracks = array();
    protected $inviteCodes = array();
//    protected $resetPassCodes = array();

    public function __construct($numElements)
    {
        $this->postgres = new PostgresSeedController(new CronConnection());
        $this->numElements = $numElements;
    }

    /**
     * @return array
     */
    public function getUsers()
    {
        if ($this->users == array()) {
            $this->setUsers();
        }

        return $this->users;
    }

    public function setUsers()
    {
        $query = "SELECT * FROM public.user LIMIT $this->numElements;";
        $this->users = $this->postgres->execute($query);
    }

    /**
     * @return array
     */
    public function getUserProfiles()
    {
        if ($this->userProfiles == array()) {
            $this->setUserProfiles();
        }

        return $this->userProfiles;
    }

    public function setUserProfiles()
    {
        $query = "SELECT * FROM public.user_profile LIMIT $this->numElements;";
        $this->userProfiles = $this->postgres->execute($query);
    }

    /**
     * @return array
     */
    public function getFriends()
    {
        if ($this->friends == array()) {
            $this->setFriends();
        }

        return $this->friends;
    }

    public function setFriends()
    {
        $query = "SELECT * FROM public.friend LIMIT $this->numElements;";
        $this->friends = $this->postgres->execute($query);
    }

    /**
     * @return array
     */
    public function getAlbums()
    {
        if ($this->albums == array()) {
            $this->setAlbums();
        }

        return $this->albums;
    }

    public function setAlbums()
    {
        $query = "SELECT * FROM public.album LIMIT $this->numElements;";
        $this->albums = $this->postgres->execute($query);
    }

    /**
     * @return array
     */
    public function getUserAlbums()
    {
        if ($this->userAlbums == array()) {
            $this->setAlbums();
        }

        return $this->userAlbums;
    }

    public function setUserAlbums()
    {
        $query = "SELECT * FROM public.user_album LIMIT $this->numElements;";
        $this->userAlbums = $this->postgres->execute($query);
    }

    /**
     * @return array
     */
    public function getGroups()
    {
        if ($this->groups == array()) {
            $this->setGroups();
        }

        return $this->groups;
    }

    public function setGroups()
    {
        $query = "SELECT * FROM public.group LIMIT $this->numElements;";
        $this->groups = $this->postgres->execute($query);
    }

    /**
     * @return array
     */
    public function getTracks()
    {
        if ($this->tracks == array()) {
            $this->setTracks();
        }

        return $this->tracks;
    }

    public function setTracks()
    {
        $query = "SELECT * FROM public.track LIMIT $this->numElements;";
        $this->tracks = $this->postgres->execute($query);
    }

    /**
     * @return array
     */
    public function getGroupProfiles()
    {
        if ($this->groupProfiles == array()) {
            $this->setGroupProfiles();
        }

        return $this->groupProfiles;
    }


    public function setGroupProfiles()
    {
        $query = "SELECT * FROM public.group_profile LIMIT $this->numElements;";
        $this->groupProfiles = $this->postgres->execute($query);
    }

    /**
     * @return array
     */
    public function getRelatedGroups()
    {
        if ($this->relatedGroups == array()) {
            $this->setRelatedGroups();
        }

        return $this->relatedGroups;
    }

    public function setRelatedGroups()
    {
        $query = "SELECT * FROM public.related_group LIMIT $this->numElements;";
        $this->relatedGroups = $this->postgres->execute($query);
    }

    /**
     * @return array
     */
    public function getUserTracks()
    {
        if ($this->userTracks == array()) {
            $this->setUserTracks();
        }

        return $this->userTracks;
    }

    public function setUserTracks()
    {
        $query = "SELECT * FROM public.user_track LIMIT $this->numElements;";
        $this->userTracks = $this->postgres->execute($query);
    }

    /**
     * @return array
     */
    public function getPlaylists()
    {
        if ($this->playlists == array()) {
            $this->setPlaylists();
        }

        return $this->playlists;
    }

    public function setPlaylists()
    {
        $query = "SELECT * FROM public.playlist LIMIT $this->numElements;";
        $this->playlists = $this->postgres->execute($query);
    }

    /**
     * @return array
     */
    public function getPlaylistFollowers()
    {
        if ($this->playlistFollowers == array()) {
            $this->setPlaylistFollowers();
        }

        return $this->playlistFollowers;
    }

    public function setPlaylistFollowers()
    {
        $query = "SELECT * FROM public.playlist_follower LIMIT $this->numElements;";
        $this->playlistFollowers = $this->postgres->execute($query);
    }

    /**
     * @return array
     */
    public function getPlaylistTracks()
    {
        if ($this->playlistTracks == array()) {
            $this->setPlaylistTracks();
        }

        return $this->playlistTracks;
    }

    public function setPlaylistTracks()
    {
        $query = "SELECT * FROM public.playlist_track LIMIT $this->numElements;";
        $this->playlistTracks = $this->postgres->execute($query);
    }

    /**
     * @return array
     */
    public function getMediaArray()
    {
        if ($this->mediaArray == array()) {
            $this->setMediaArray();
        }

        return $this->mediaArray;
    }

    public function setMediaArray()
    {
        $query = "SELECT * FROM public.media LIMIT $this->numElements;";
        $this->mediaArray = $this->postgres->execute($query);
    }

    /**
     * @return array
     */
    public function getUserGroups()
    {
        if ($this->userGroups == array()) {
            $this->setUserGroup();
        }

        return $this->userGroups;
    }

    public function setUserGroup()
    {
        $query = "SELECT * FROM public.user_group LIMIT $this->numElements;";
        $this->userGroups = $this->postgres->execute($query);
    }

    /**
     * @return array
     */
    public function getTrackPlays()
    {
        if ($this->trackPlays == array()) {
            $this->setTrackPlays();
        }

        return $this->trackPlays;
    }

    public function setTrackPlays()
    {
        $query = "SELECT * FROM public.track_play LIMIT $this->numElements;";
        $this->trackPlays = $this->postgres->execute($query);
    }

    /**
     * @return array
     */
    public function getAlbumTracks()
    {
        if ($this->albumTracks == array()) {
            $this->setAlbumTracks();
        }

        return $this->albumTracks;
    }

    public function setAlbumTracks()
    {
        $query = "SELECT * FROM public.album_track LIMIT $this->numElements;";
        $this->albumTracks = $this->postgres->execute($query);
    }

    /**
     * @return array
     */
    public function getInviteCodes()
    {
        if ($this->inviteCodes == array()) {
            $this->setInviteCodes();
        }

        return $this->inviteCodes;
    }

    public function setInviteCodes()
    {
        $query = "SELECT * FROM public.invite_code LIMIT $this->numElements;";
        $this->inviteCodes = $this->postgres->execute($query);
    }

    /**
     * @return array
     */
    public function getResetPassCode()
    {
        if ($this->resetPassCodes == array()) {
            $this->setResetPassCodes();
        }

        return $this->resetPassCodes;
    }

//    public function setResetPassCodes()
//    {
//        $query = "SELECT * FROM public.reset_pass_code LIMIT $this->numElements;";
//        $this->resetPassCodes = $this->postgres->execute($query);
//    }
}