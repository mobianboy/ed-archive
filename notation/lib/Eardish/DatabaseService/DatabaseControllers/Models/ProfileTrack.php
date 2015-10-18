<?php
namespace Eardish\DatabaseService\DatabaseControllers\Models;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping;
use Eardish\DatabaseService\DatabaseControllers\Models\Traits\Timestamp;
/**
 * Class ProfileTrack
 *
 * @package Eardish\DatabaseService\DatabaseControllers\Models
 * @Entity @Table(name="profile_track")
 */
class ProfileTrack
{
    use Timestamp;
    /**
     * @Id
     * @Column(type="integer", unique=true)
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="Eardish\DatabaseService\DatabaseControllers\Models\Profile", inversedBy="user_track")
     * @JoinColumn(name="profile_id", referencedColumnName="id")
     */
    protected $profile;

    /**
     * @ManyToOne(targetEntity="Eardish\DatabaseService\DatabaseControllers\Models\Track", inversedBy="user_track")
     * @JoinColumn(name="track_id", referencedColumnName="id")
     */
    protected $track;

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
    public function getTrack()
    {
        return $this->track;
    }

    /**
     * @param mixed $track
     */
    public function setTrack($track)
    {
        $this->track = $track;
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