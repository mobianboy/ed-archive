<?php
namespace Eardish\DatabaseService\DatabaseControllers\Models;

use Eardish\DatabaseService\DatabaseControllers\Models\Traits\Timestamp;
use Doctrine\ORM\Mapping;
/**
 * Class TrackRating
 *
 * @package Eardish\DatabaseService\DatabaseControllers\Models
 * @Entity @Table(name="track_rating")
 */
class TrackRating
{
    use Timestamp;
    /**
     * @Id
     * @Column(type="integer", unique=true)
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="Eardish\DatabaseService\DatabaseControllers\Models\Track", inversedBy="track_rating")
     * @JoinColumn(name="track_id", referencedColumnName="id")
     */
    protected $track_id;

    /**
     * @ManyToOne(targetEntity="Eardish\DatabaseService\DatabaseControllers\Models\Profile", inversedBy="track_rating")
     * @JoinColumn(name="profile_id", referencedColumnName="id")
     */
    protected $profile_id;

    /**
     * @column(type="integer")
     */
    protected $rating;

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
    public function getTrackId()
    {
        return $this->track_id;
    }

    /**
     * @param mixed $track_id
     */
    public function setTrackId($track_id)
    {
        $this->track_id = $track_id;
    }

    /**
     * @return mixed
     */
    public function getProfileId()
    {
        return $this->profile_id;
    }

    /**
     * @param mixed $profile_id
     */
    public function setProfileId($profile_id)
    {
        $this->profile_id = $profile_id;
    }

    /**
     * @return mixed
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @param mixed $rating
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
    }

}