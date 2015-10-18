<?php
namespace Eardish\DatabaseService\DatabaseControllers\Models;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping;
use Eardish\DatabaseService\DatabaseControllers\Models\Traits\Timestamp;
/**
 * Class Genre
 *
 * @package Eardish\DatabaseService\DatabaseControllers\Models
 * @Entity @Table(name="genre")
 */
class Genre
{
    use Timestamp;
    /**
     * @Id
     * @Column(type="integer", unique=true)
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @Column(type="string")
     */
    protected $name;

    /**
     * @var ArrayCollection
     * @OneToMany(targetEntity="TrackGenre", mappedBy="genre")
     */
    protected $genre_tracks;

    /**
     * @var ArrayCollection
     * @OneToMany(targetEntity="ProfileGenre", mappedBy="genre")
     */
    protected $genre_profile;

    /**
     * @var ArrayCollection
     * @OneToMany(targetEntity="ProfileGenreBlend", mappedBy="genre")
     */
    protected $profile_genre_blend;


    public function __construct()
    {
        $this->genre_tracks = new ArrayCollection();
        $this->profile_genre_blend = new ArrayCollection();
        $this->genre_profile = new ArrayCollection();
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
    public function getGenre()
    {
        return $this->genre;
    }

    /**
     * @param mixed $genre
     */
    public function setGenre($genre)
    {
        $this->genre = $genre;
    }

    /**
     * @return ArrayCollection
     */
    public function getGenreTracks()
    {
        return $this->genre_tracks;
    }

    /**
     * @param ArrayCollection $genre_tracks
     */
    public function setGenreTracks($genre_tracks)
    {
        $this->genre_tracks = $genre_tracks;
    }

    /**
     * @return ArrayCollection
     */
    public function getProfileGenreBlend()
    {
        return $this->profile_genre_blend;
    }

    /**
     * @param ArrayCollection $profile_genre_blend
     */
    public function setProfileGenreBlend($profile_genre_blend)
    {
        $this->profile_genre_blend = $profile_genre_blend;
    }
}
