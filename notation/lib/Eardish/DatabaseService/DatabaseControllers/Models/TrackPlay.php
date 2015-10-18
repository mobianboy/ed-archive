<?php
namespace Eardish\DatabaseService\DatabaseControllers\Models;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping;
use Doctrine\Tests\ORM\Functional\Ticket\DateTime2;

/**
 * Class TrackPlay
 *
 * @package Eardish\DatabaseService\DatabaseControllers\Models
 * @Entity @Table(name="track_play")
 */
class TrackPlay
{
    /**
     * @Id
     * @Column(type="integer", unique=true)
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="Eardish\DatabaseService\DatabaseControllers\Models\Profile", inversedBy="track_play")
     * @JoinColumn(name="profile_id", referencedColumnName="id")
     */
    protected $profile;

    /**
     * @ManyToOne(targetEntity="Eardish\DatabaseService\DatabaseControllers\Models\Track", inversedBy="track_play")
     * @JoinColumn(name="track_id", referencedColumnName="id")
     */
    protected $track;

    /**
     * @Column(type="datetime", nullable=true)
     */
    protected $date_created = null;


    public function __construct($data)
    {
        $this->posted_at = new \DateTime();
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
    public function getPostedAt()
    {
        return $this->posted_at;
    }

    /**
     * @param mixed $posted_at
     */
    public function setPostedAt($posted_at)
    {
        $this->posted_at = $posted_at;
    }
}

