<?php
namespace Eardish\DatabaseService\DatabaseControllers\Models;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping;
use Eardish\DatabaseService\DatabaseControllers\Models\Traits\Timestamp;
/**
 * Class Track
 *
 * @package Eardish\DatabaseService\DatabaseControllers\Models
 * @Entity @Table(name="track")
 */
class Track
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
     * @ManyToOne(targetEntity="Eardish\DatabaseService\DatabaseControllers\Models\Profile", inversedBy="track")
     * @JoinColumn(name="profile_id", referencedColumnName="id")
     */
    protected $profile_id;

    /**
     * @ManyToOne(targetEntity="Eardish\DatabaseService\DatabaseControllers\Models\Art", inversedBy="track")
     * @JoinColumn(name="art_id", referencedColumnName="id")
     */
    protected $art_id;

    /**
     * @Column(type="integer", nullable=true)
     */
    protected $length = null;

    /**
     * @column(type="boolean", nullable=true)
     */
    protected $deleted = false;

    /**
     * @column(type="boolean", nullable=true)
     */
    protected $published = false;

    /**
     * @var ArrayCollection
     * @OneToMany(targetEntity="Audio", mappedBy="track")
     */
    private $audio;

    /**
     * @var ArrayCollection
     * @OneToMany(targetEntity="AlbumTrack", mappedBy="track")
     */
    private $album_tracks;

    /**
     * @var ArrayCollection
     * @OneToMany(targetEntity="ProfileTrack", mappedBy="track")
     */
    private $profile_tracks;

    /**
     * @var ArrayCollection
     * @OneToMany(targetEntity="TrackPlay", mappedBy="track")
     */
    private $track_plays;

    /**
     * @var ArrayCollection
     * @OneToMany(targetEntity="Analytic", mappedBy="track")
     */
    private $analytic_tracks;

    /**
     * @var ArrayCollection
     * @OneToMany(targetEntity="TrackRating", mappedBy="track")
     */
    private $track_ratings;

    /**
     * @var ArrayCollection
     * @OneToMany(targetEntity="TranscodeJob", mappedBy="track")
     */
    private $transcode_job_track;


    public function __construct()
    {
        $this->audio = new ArrayCollection();
        $this->album_tracks = new ArrayCollection();
        $this->profile_tracks = new ArrayCollection();
        $this->track_plays = new ArrayCollection();
        $this->analytic_tracks = new ArrayCollection();
        $this->track_ratings = new ArrayCollection();
        $this->transcode_job_track = new ArrayCollection();

    }

}