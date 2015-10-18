<?php
namespace Eardish\DatabaseService\DatabaseControllers\Models;

use Doctrine\ORM\Mapping;
use Eardish\DatabaseService\DatabaseControllers\Models\Traits\Timestamp;
/**
 * Class PlaylistFollower
 *
 * @package Eardish\DatabaseService\DatabaseControllers\Models
 * @Entity @Table(name="playlist_follower")
 */
class PlaylistFollower
{
    use Timestamp;
    /**
     * @Id
     * @Column(type="integer", unique=true)
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="Eardish\DatabaseService\DatabaseControllers\Models\Playlist", inversedBy="playlist_follower")
     * @JoinColumn(name="playlist_id", referencedColumnName="id")
     */
    protected $playlist;

    /**
     * @ManyToOne(targetEntity="Eardish\DatabaseService\DatabaseControllers\Models\Profile", inversedBy="playlist_follower")
     * @JoinColumn(name="follower_id", referencedColumnName="id")
     */
    protected $follower;


    public function __construct()
    {
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getPlaylist()
    {
        return $this->playlist;
    }

    /**
     * @param mixed $playlist
     */
    public function setPlaylist($playlist)
    {
        $this->playlist = $playlist;
    }

    /**
     * @return mixed
     */
    public function getFollower()
    {
        return $this->follower;
    }

    /**
     * @param mixed $follower
     */
    public function setFollower($follower)
    {
        $this->follower = $follower;
    }
}
