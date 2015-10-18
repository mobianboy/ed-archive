<?php
namespace Eardish\DatabaseService\DatabaseControllers\Models;

use Doctrine\ORM\Mapping;
use Eardish\DatabaseService\DatabaseControllers\Models\Traits\Timestamp;
/**
 * Class TrackGenre
 *
 * @package Eardish\DatabaseService\DatabaseControllers\Models
 * @Entity @Table(name="track_genre")
 */
class TrackGenre
{
    use Timestamp;
    /**
     * @Id
     * @Column(type="integer", unique=true)
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="Eardish\DatabaseService\DatabaseControllers\Models\Track", inversedBy="track_genre")
     * @JoinColumn(name="track_id", referencedColumnName="id")
     */
    protected $track_id;

    /**
     * @ManyToOne(targetEntity="Eardish\DatabaseService\DatabaseControllers\Models\Genre", inversedBy="track_genre")
     * @JoinColumn(name="genre_id", referencedColumnName="id")
     */
    protected $genre_id;


    /**
     * @return mixed
     */
    public function getGenreId()
    {
        return $this->genre_id;
    }

    /**
     * @param mixed $genre_id
     */
    public function setGenreId($genre_id)
    {
        $this->genre_id = $genre_id;
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
}
