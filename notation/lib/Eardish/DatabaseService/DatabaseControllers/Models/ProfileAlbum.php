<?php
namespace Eardish\DatabaseService\DatabaseControllers\Models;

use Doctrine\ORM\Mapping;
use Eardish\DatabaseService\DatabaseControllers\Models\Traits\Timestamp;
/**
 * Class ProfileAlbum
 *
 * @package Eardish\DatabaseService\DatabaseControllers\Models
 * @Entity @Table(name="profile_album")
 */
class ProfileAlbum
{
    use Timestamp;
    /**
     * @Id
     * @Column(type="integer", unique=true)
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="Eardish\DatabaseService\DatabaseControllers\Models\Profile", inversedBy="user_album")
     * @JoinColumn(name="profile_id", referencedColumnName="id")
     */
    protected $profile;

    /**
     * @ManyToOne(targetEntity="Eardish\DatabaseService\DatabaseControllers\Models\Album", inversedBy="user_album")
     * @JoinColumn(name="album_id", referencedColumnName="id")
     */
    protected $album;

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
    public function getAlbum()
    {
        return $this->album;
    }

    /**
     * @param mixed $album
     */
    public function setAlbum($album)
    {
        $this->album = $album;
    }

    /**
     * @return mixed
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * @param mixed $profile
     */
    public function setProfile($profile)
    {
        $this->profile = $profile;
    }
}