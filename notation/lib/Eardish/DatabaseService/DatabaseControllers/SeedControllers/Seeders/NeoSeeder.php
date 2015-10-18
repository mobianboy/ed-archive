<?php
namespace Eardish\DatabaseService\DatabaseControllers\SeedControllers\Seeders;

use Eardish\DatabaseService\DatabaseControllers\SeedControllers\NeoSeedController;
use Eardish\DatabaseService\DatabaseControllers\SeedControllers\Seeders\Core\PostgresData;
use Everyman\Neo4j\Node;

class NeoSeeder extends \SeedData
{
    protected $postgresData;

    public function __construct($numElements)
    {
        $this->numElements = $numElements;
        $this->database = new NeoSeedController();
        $this->postgresData = new PostgresData($numElements);
    }

    public function seed()
    {
        $this->seedUsers();
        $this->seedUserProfiles();
        $this->seedAlbums();
        $this->seedGroups();
        $this->seedTracks();
        $this->seedGroupProfiles();
        $this->seedPlaylists();
        $this->seedMedia();

        $this->userAlbumRelation();
        $this->userGroupRelation();
        $this->groupProfileRelation();
        $this->friendRelation();
        $this->userTrackRelation();
        $this->userProfileRelation();
        $this->relatedGroupRelation();
        $this->playlistFollowerRelation();
        $this->playlistTrackRelation();
        $this->trackPlayRelation();
        $this->mediaTrackRelation();
    }

    public function seedUsers()
    {
        foreach($this->postgresData->getUsers() as $user) {

            $this->database->insertSeed("user", $user);
        }
    }

    public function seedUserProfiles()
    {
        foreach($this->postgresData->getUserProfiles() as $userProfile) {
            $this->database->insertSeed("user_profile", $userProfile);
        }
    }

    public function seedAlbums()
    {
        foreach($this->postgresData->getAlbums() as $album) {
            $this->database->insertSeed("album", $album);
        }
    }

    public function seedGroups()
    {
        foreach($this->postgresData->getGroups() as $group) {
            $this->database->insertSeed("group", $group);
        }
    }

    public function seedGroupProfiles()
    {
        foreach($this->postgresData->getGroupProfiles() as $groupProfile) {
            $this->database->insertSeed("group_profile", $groupProfile);
        }
    }

    public function seedTracks()
    {
        foreach($this->postgresData->getTracks() as $track) {
            $this->database->insertSeed("track", $track);
        }
    }


    public function seedMedia()
    {
        foreach($this->postgresData->getMediaArray() as $media) {
            $this->database->insertSeed("media", $media);
        }
    }

    public function seedPlaylists()
    {
        foreach ($this->postgresData->getPlaylists() as $playlist) {
            $this->database->insertSeed("playlist", $playlist);
        }
    }

    public function userProfileRelation()
    {
        foreach ($this->postgresData->getUsers() as $user) {
            foreach ($this->postgresData->getUserProfiles() as $userProfile) {
                if ($user['id'] == $userProfile['user_id']) {
                    $userProfileId = $userProfile['user_id'];
                    $userId = $user['id'];

                    $userNode = $this->database->getNode('user', 'id', $userId);
                    $userProfileNode = $this->database->getNode('user_profile', 'id', $userProfileId);

                    $this->database->makeRelation($userNode, $userProfileNode, 'HAS_PROFILE', array());
                }
            }
        }
    }

    public function friendRelation()
    {
        foreach($this->postgresData->getUsers() as $user) {
            foreach ($this->postgresData->getFriends() as $friend) {
                if ($friend['user_id'] == $user['id'] ) {
                    $userId = $user['id'];
                    $friendId = $friend['friend_id'];

                    $userNode = $this->database->getNode('user', 'id', $userId);
                    $friendNode = $this->database->getNode('user', 'id', $friendId);
                    $propertyId = $friend['id'];
                    $this->database->makeRelation($userNode, $friendNode, "FRIEND", array('id'=> $propertyId));
                }
            }
        }
    }

    public function userGroupRelation()
    {
        foreach($this->postgresData->getUserGroups() as $userGroup) {
            $groupId = $userGroup['group_id'];
            $groupNode = $this->database->getNode('group', 'id', $groupId);
            $propertyId = $userGroup['id'];
            if (empty($userGroup['acting_as_id'])) {
                $userId = $userGroup['user_id'];
                $userNode = $this->database->getNode('user', 'id', $userId);
                $this->database->makeRelation($userNode, $groupNode, "USER_GROUP", array('id'=> $propertyId));
            } else {
                // if the user is acting as a group, use the group node
                $actingAsID = $userGroup['acting_as_id'];
                $actingAsNode = $this->database->getNode('group', 'id', $actingAsID);
                $this->database->makeRelation($actingAsNode, $groupNode, "USER_GROUP", array('id'=> $propertyId));
            }
        }
    }

    public function groupProfileRelation()
    {
        foreach ($this->postgresData->getGroups() as $group) {
            foreach ($this->postgresData->getGroupProfiles() as $groupProfile) {
                if ($group['id'] == $groupProfile['group_id']) {
                    $groupProfileId = $groupProfile['group_id'];
                    $groupId = $group['id'];
                    $groupNode = $this->database->getNode('group', 'id', $groupId);
                    $groupProfileNode = $this->database->getNode('group_profile', 'id', $groupProfileId);
                    $this->database->makeRelation($groupNode, $groupProfileNode, 'HAS_PROFILE', array());
                }
            }
        }
    }

    public function relatedGroupRelation()
    {
        foreach($this->postgresData->getGroups() as $group) {
            foreach ($this->postgresData->getRelatedGroups() as $relatedGroup) {
                if ($relatedGroup['group1_id'] == $group['id'] ) {
                    $groupId = $group['id'];
                    $relatedGroupId = $relatedGroup['group2_id'];
                    $groupNode = $this->database->getNode('group', 'id', $groupId);
                    $relatedGroupNode = $this->database->getNode('group', 'id', $relatedGroupId);

                    $propertyId = $relatedGroup['id'];
                    $this->database->makeRelation($groupNode, $relatedGroupNode, "RELATED_GROUP", array('id'=> $propertyId));
                }
            }
        }
    }

    public function userAlbumRelation()
    {
        foreach($this->postgresData->getUserAlbums() as $userAlbum) {
            $albumId = $userAlbum['album_id'];
            $albumNode = $this->database->getNode('album', 'id', $albumId);
            $propertyId = $userAlbum['id'];
            if (empty($userAlbum['acting_as_id'])) {
                $userId = $userAlbum['user_id'];
                $userNode = $this->database->getNode('user', 'id', $userId);
                $this->database->makeRelation($userNode, $albumNode, "USER_ALBUM", array('id'=> $propertyId));
            } else {
                // if the user is acting as a group, use the group node
                $groupID = $userAlbum['acting_as_id'];
                $groupNode = $this->database->getNode('group', 'id', $groupID);
                $this->database->makeRelation($groupNode, $albumNode, "USER_ALBUM", array('id'=> $propertyId));
            }
        }
        // MATCH (a)-[:`USER_ALBUM`]->(b:Album) RETURN a,b LIMIT 25
    }

    public function playlistFollowerRelation()
    {
        foreach ($this->postgresData->getPlaylistFollowers() as $playlistFollower) {
            $playlistId = $playlistFollower['playlist_id'];
            $playlistNode = $this->database->getNode('playlist', 'id', $playlistId);
            $propertyId = $playlistFollower['id'];
            if (empty($playlistFollower['acting_as_id'])) {
                $followerId = $playlistFollower['follower_id'];
                $followerNode = $this->database->getNode('user', 'id', $followerId);
                $this->database->makeRelation($followerNode, $playlistNode, "PLAYLIST_FOLLOWER", array('id'=> $propertyId));
            } else {
                $groupId = $playlistFollower['acting_as_id'];
                $groupNode = $this->database->getNode('group', 'id', $groupId);
                $this->database->makeRelation($groupNode, $playlistNode, 'PLAYLIST_FOLLOWER', array('id'=> $propertyId));
            }
        }
    }

    public function playlistTrackRelation()
    {
        foreach ($this->postgresData->getPlaylistTracks() as $playlistTrack) {
            $trackId = $playlistTrack['track_id'];
            $playlistId = $playlistTrack['playlist_id'];
            $propertyId = $playlistTrack['id'];
            $trackNode = $this->database->getNode('track', 'id', $trackId);
            $playlistNode = $this->database->getNode('playlist', 'id', $playlistId);
            $this->database->makeRelation($playlistNode, $trackNode, "PLAYLIST_TRACK", array('id'=> $propertyId));
        }
    }

    public function trackPlayRelation()
    {
        foreach ($this->postgresData->getTrackPlays() as $trackPlay) {
            $trackId = $trackPlay['track_id'];
            $userId = $trackPlay['user_id'];
            $propertyId = $trackPlay['id'];
            $postedAt = $trackPlay['posted_at'];
            $trackNode = $this->database->getNode('track', 'id', $trackId);
            $userNode = $this->database->getNode('user', 'id', $userId);
            $this->database->makeRelation($userNode, $trackNode, "TRACK_PLAY", array('id'=> $propertyId, 'posted_at' => $postedAt));
        }
    }

    public function mediaTrackRelation()
    {
        foreach($this->postgresData->getMediaArray() as $media) {
            foreach ($this->postgresData->getTracks() as $track) {
                if ($media['track_id'] == $track['id'] ) {
                    $trackId = $track['id'];
                    $mediaId = $media['id'];
                    $trackNode = $this->database->getNode('track', 'id', $trackId);
                    $mediaNode = $this->database->getNode('media', 'id', $mediaId);

                    $this->database->makeRelation($trackNode, $mediaNode, "HAS_MEDIA", array());
                }
            }
        }
    }

    public function albumTrackRelation()
    {
        foreach ($this->postgresData->getAlbumTracks() as $albumTrack) {
            $trackId = $albumTrack['track_id'];
            $albumId = $albumTrack['album_id'];
            $propertyId = $albumTrack['id'];
            $trackNode = $this->database->getNode('track', 'id', $trackId);
            $albumNode = $this->database->getNode('album', 'id', $albumId);
            $this->database->makeRelation($albumNode, $trackNode, "ALBUM_TRACK", array('id'=> $propertyId));
        }
    }

    public function userTrackRelation()
    {
        foreach($this->postgresData->getUserTracks() as $userTrack) {
            $trackId = $userTrack['track_id'];
            $trackNode = $this->database->getNode('track', 'id', $trackId);
            $propertyId = $userTrack['id'];
            if (empty($userTrack['acting_as_id'])) {
                $userId = $userTrack['user_id'];
                $userNode = $this->database->getNode('user', 'id', $userId);
                $this->database->makeRelation($userNode, $trackNode, "USER_TRACK", array('id'=> $propertyId));
            } else {
                // if the user is acting as a group, use the group node
                $groupID = $userTrack['acting_as_id'];
                $groupNode = $this->database->getNode('group', 'id', $groupID);
                $this->database->makeRelation($groupNode, $trackNode, "USER_TRACK", array('id'=> $propertyId));
            }
        }
        // MATCH (a)-[:`USER_TRACK`]->(b:Track) RETURN a,b LIMIT 25
    }
}
