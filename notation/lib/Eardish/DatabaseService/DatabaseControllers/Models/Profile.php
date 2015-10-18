<?php
namespace Eardish\DatabaseService\DatabaseControllers\Models;

use Doctrine\Common\Collections\ArrayCollection;
use Eardish\DatabaseService\DatabaseControllers\Models\Traits\Timestamp;
/**
 * Class Profile
 *
 * @package Eardish\DatabaseService\DatabaseControllers\Models
 * @Entity @Table(name="profile")
 */
class Profile
{
    use Timestamp;
    /**
     * @Id
     * @Column(type="integer", unique=true)
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="Eardish\DatabaseService\DatabaseControllers\Models\User", inversedBy="profile")
     * @JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user_id;

    /**
     * @ManyToOne(targetEntity="Eardish\DatabaseService\DatabaseControllers\Models\Art", inversedBy="profile")
     * @JoinColumn(name="art_id", referencedColumnName="id")
     */
    protected $art = null;

    /**
     * @ManyToOne(targetEntity="Eardish\DatabaseService\DatabaseControllers\Models\Contact", inversedBy="profile")
     * @JoinColumn(name="contact_id", referencedColumnName="id")
     */
    protected $contact = null;

    /**
     * @Column(type="boolean", nullable=true)
     */
    protected $onboarded = false;

    /**
     * @Column(type="string")
     */
    protected $type;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $invite_code;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $first_name = null;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $last_name = null;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $artist_name = null;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $year_of_birth = null;

    /**
     * @Column(type="text", nullable=true)
     */
    protected $bio = null;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $website = null;

    /**
     * @Column(type="integer", nullable=true)
     */
    protected $year_founded = null;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $influenced_by = null;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $hometown = null;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $facebook_page = null;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $twitter_page = null;

    /**
     * @ManyToOne(targetEntity="Eardish\DatabaseService\DatabaseControllers\Models\Profile", inversedBy="profile")
     * @JoinColumn(name="ar_rep", referencedColumnName="id")
     */
    protected $ar_rep;

    /**
     * @ManyToOne(targetEntity="Eardish\DatabaseService\DatabaseControllers\Models\Profile", inversedBy="profile")
     * @JoinColumn(name="last_edited_by", referencedColumnName="id")
     */
    protected $last_edited_by;

    /**
     * @var ArrayCollection
     * @OneToMany(targetEntity="Art", mappedBy="profile")
     */
    private $arts;

    /**
     * @var ArrayCollection
     * @OneToMany(targetEntity="Track", mappedBy="profile")
     */
    private $track;

    /**
     * @var ArrayCollection
     * @OneToMany(targetEntity="Friend", mappedBy="profile")
     */
    private $friend1;

    /**
     * @var ArrayCollection
     * @OneToMany(targetEntity="Friend", mappedBy="profile")
     */
    private $friend2;

    /**
     * @var ArrayCollection
     * @OneToMany(targetEntity="BandMember", mappedBy="profile")
     */
    private $band_member1;

    /**
     * @var ArrayCollection
     * @OneToMany(targetEntity="BandMember", mappedBy="profile")
     */
    private $band_member2;

    /**
     * @var ArrayCollection
     * @OneToMany(targetEntity="PlaylistFollower", mappedBy="profile")
     */
    private $playlist_followers;

    /**
     * @var ArrayCollection
     * @OneToMany(targetEntity="Playlist", mappedBy="profile")
     */
    private $playlist_creators;

    /**
     * @var ArrayCollection
     * @OneToMany(targetEntity="TrackPlay", mappedBy="profile")
     */
    private $track_play_profile;

    /**
     * @var ArrayCollection
     * @OneToMany(targetEntity="ProfileBadge", mappedBy="profile")
     */
    private $profile_badges;

    /**
     * @var ArrayCollection
     * @OneToMany(targetEntity="ProfileGenre", mappedBy="profile")
     */
    private $profile_genres;

    /**
     * @var ArrayCollection
     * @OneToMany(targetEntity="ProfileGenreBlend", mappedBy="profile")
     */
    private $profile_genre_blend;

    /**
     * @var ArrayCollection
     * @OneToMany(targetEntity="Track", mappedBy="profile")
     */
    private $track_creators;

    /**
     * @var ArrayCollection
     * @OneToMany(targetEntity="Analytic", mappedBy="profile")
     */
    private $profile_analytic;

    /**
     * @var ArrayCollection
     * @OneToMany(targetEntity="Analytic", mappedBy="profile")
     */
    private $artist_analytic;

    /**
     * @var ArrayCollection
     * @OneToMany(targetEntity="Profile", mappedBy="profile")
     */
    private $last_edits;

    public function __construct()
    {
        $this->arts = new ArrayCollection();
        $this->track = new ArrayCollection();
        $this->band_member1 = new ArrayCollection();
        $this->band_member2 = new ArrayCollection();
        $this->friend1 = new ArrayCollection();
        $this->friend2 = new ArrayCollection();
        $this->playlist_followers = new ArrayCollection();
        $this->playlist_creators = new ArrayCollection();
        $this->track_play_profile = new ArrayCollection();
        $this->profile_badges = new ArrayCollection();
        $this->profile_genre_blend = new ArrayCollection();
        $this->track_creators = new ArrayCollection();
        $this->profile_genres = new ArrayCollection();
        $this->profile_analytic = new ArrayCollection();
        $this->artist_analytic = new ArrayCollection();
        $this->last_edits = new ArrayCollection();
    }
}