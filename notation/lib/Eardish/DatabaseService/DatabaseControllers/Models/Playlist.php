<?php
namespace Eardish\DatabaseService\DatabaseControllers\Models;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping;
use Eardish\DatabaseService\DatabaseControllers\Models\Traits\Timestamp;
/**
 * Class Playlist
 *
 * @package Eardish\DatabaseService\DatabaseControllers\Models
 * @Entity @Table(name="playlist")
 */
class Playlist
{
    use Timestamp;
    /**
     * @Id
     * @Column(type="integer", unique=true)
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="Eardish\DatabaseService\DatabaseControllers\Models\Profile", inversedBy="playlist")
     * @JoinColumn(name="profile_id", referencedColumnName="id")
     */
    protected $profile;

    /**
     * @Column(type="string")
     */
    protected $name;

    /**
     * @var ArrayCollection
     * @OneToMany(targetEntity="PlaylistFollower", mappedBy="playlist")
     */
    private $playlist_followers;

    public function __construct()
    {
        $this->created_at = new \DateTime();
        $this->playlist_followers = new ArrayCollection();
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
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param mixed $created_at
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }

    /**
     * @return ArrayCollection
     */
    public function getPlaylistFollowers()
    {
        return $this->playlist_followers;
    }

    /**
     * @param ArrayCollection $playlist_followers
     */
    public function setPlaylistFollowers($playlist_followers)
    {
        $this->playlist_followers = $playlist_followers;
    }

    /**
     * @return boolean
     */
    public function getDeleteStatus()
    {
        return $this->deleted;
    }

    /**
     * @param boolean
     */
    public function setDeleteStatus($flag)
    {
        $this->deleted = $flag;
    }
}
