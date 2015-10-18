<?php
namespace Eardish\DatabaseService\DatabaseControllers\Models;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping;
use Eardish\DatabaseService\DatabaseControllers\Models\Traits\Timestamp;
/**
 * Class PlaylistTrack
 *
 * @package Eardish\DatabaseService\DatabaseControllers\Models
 * @Entity @Table(name="playlist_track")
 */
class PlaylistTrack
{
    use Timestamp;
    /**
     * @Id
     * @Column(type="integer", unique=true)
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="Eardish\DatabaseService\DatabaseControllers\Models\Playlist", inversedBy="playlist_track")
     * @JoinColumn(name="playlist_id", referencedColumnName="id")
     */
    protected $playlist;

    /**
     * @ManyToOne(targetEntity="Eardish\DatabaseService\DatabaseControllers\Models\Track", inversedBy="playlist_track")
     * @JoinColumn(name="track_id", referencedColumnName="id")
     */
    protected $track;

    /**
     * @Column(type="integer")
     */
    protected $track_position;

    public function __construct()
    {
        $this->added_at = new \DateTime();
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
    public function getTrackPosition()
    {
        return $this->track_position;
    }

    /**
     * @param mixed $track_position
     */
    public function setTrackPosition($track_position)
    {
        $this->track_position = $track_position;
    }

    /**
     * @return mixed
     */
    public function getAddedAt()
    {
        return $this->added_at;
    }

    /**
     * @param mixed $added_at
     */
    public function setAddedAt($added_at)
    {
        $this->added_at = $added_at;
    }
}
