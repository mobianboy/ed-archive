<?php
namespace Eardish\DatabaseService\DatabaseControllers\Models;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping;
use Symfony\Component\Yaml\Tests\A;
use Eardish\DatabaseService\DatabaseControllers\Models\Traits\Timestamp;
/**
 * Class Album
 *
 * @package Eardish\DatabaseService\DatabaseControllers\Models
 * @Entity @Table(name="album")
 */
class Album
{
    use Timestamp;
    /**
     * @Id
     * @Column(type="integer", unique=true)
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="Eardish\DatabaseService\DatabaseControllers\Models\Profile", inversedBy="album")
     * @JoinColumn(name="profile_id", referencedColumnName="id")
     */
    protected $profile;

    /**
     * @ManyToOne(targetEntity="Eardish\DatabaseService\DatabaseControllers\Models\Art", inversedBy="album")
     * @JoinColumn(name="art_id", referencedColumnName="id")
     */
    protected $album_art = null;

    /**
     * @Column(type = "string")
     */
    protected $name;

    /**
     * @Column(type="datetime", nullable=true)
     */
    protected $release_date = null;

    /**
     * @Column(type = "boolean", nullable=true)
     */
    protected $various_artist = false;

    /**
     * @Column(type = "string", nullable=true)
     */
    protected $record_label = null;

    /**
     * @var ArrayCollection
     * @OneToMany(targetEntity="AlbumTrack", mappedBy="album")
     */
    private $album_tracks;

    /**
     * @var ArrayCollection
     * @OneToMany(targetEntity="ProfileAlbum", mappedBy="album")
     */
    private $profile_albums;

    public function __construct()
    {
        $this->album_tracks = new ArrayCollection();
        $this->profile_albums = new ArrayCollection();
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
    public function getReleaseDate()
    {
        return $this->release_date;
    }

    /**
     * @param mixed $release_date
     */
    public function setReleaseDate($release_date)
    {
        $this->release_date = $release_date;
    }

    /**
     * @return mixed
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param mixed $group
     */
    public function setGroup($group)
    {
        $this->group = $group;
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
    public function getVariousArtist()
    {
        return $this->various_artist;
    }

    /**
     * @param mixed $various_artist
     */
    public function setVariousArtist($various_artist)
    {
        $this->various_artist = $various_artist;
    }

    /**
     * @return mixed
     */
    public function getAlbumArt()
    {
        return $this->album_art;
    }

    /**
     * @param mixed $album_art
     */
    public function setAlbumArt($album_art)
    {
        $this->album_art = $album_art;
    }

    /**
     * @return mixed
     */
    public function getRecordLabel()
    {
        return $this->record_label;
    }

    /**
     * @param mixed $record_label
     */
    public function setRecordLabel($record_label)
    {
        $this->record_label = $record_label;
    }

    /**
     * @return ArrayCollection
     */
    public function getAlbumTracks()
    {
        return $this->album_tracks;
    }

    /**
     * @param ArrayCollection $album_tracks
     */
    public function setAlbumTracks($album_tracks)
    {
        $this->album_tracks = $album_tracks;
    }

    /**
     * @return ArrayCollection
     */
    public function getUserAlbums()
    {
        return $this->user_albums;
    }

    /**
     * @param ArrayCollection $user_albums
     */
    public function setUserAlbums($user_albums)
    {
        $this->user_albums = $user_albums;
    }
}
