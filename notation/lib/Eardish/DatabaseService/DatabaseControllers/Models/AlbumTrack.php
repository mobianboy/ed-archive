<?php
namespace Eardish\DatabaseService\DatabaseControllers\Models;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping;
use Eardish\DatabaseService\DatabaseControllers\Models\Traits\Timestamp;
/**
 * Class AlbumTrack
 *
 * @package Eardish\DatabaseService\DatabaseControllers\Models
 * @Entity @Table(name="album_track")
 */
class AlbumTrack
{
    use Timestamp;
    /**
     * @Id
     * @Column(type="integer", unique=true)
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="Eardish\DatabaseService\DatabaseControllers\Models\Album", inversedBy="album_track")
     * @JoinColumn(name="album_id", referencedColumnName="id")
     */
    protected $album;

    /**
     * @ManyToOne(targetEntity="Eardish\DatabaseService\DatabaseControllers\Models\Track", inversedBy="album_track")
     * @JoinColumn(name="track_id", referencedColumnName="id")
     */
    protected $track;

    /**
     * @Column(type="integer")
     */
    protected $track_num;

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
    public function getTrackNum()
    {
        return $this->track_num;
    }

    /**
     * @param mixed $track_num
     */
    public function setTrackNum($track_num)
    {
        $this->track_num = $track_num;
    }
}
