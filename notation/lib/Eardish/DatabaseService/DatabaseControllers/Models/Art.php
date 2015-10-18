<?php
namespace Eardish\DatabaseService\DatabaseControllers\Models;

use Eardish\DatabaseService\DatabaseControllers\Models\Traits\Timestamp;
use Doctrine\ORM\Mapping;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Art
 *
 * @package Eardish\DatabaseService\DatabaseControllers\Models
 * @Entity @Table(name="Art")
 */
class Art
{
    use Timestamp;
    /**
     * @Id
     * @Column(type="integer", unique=true)
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="Eardish\DatabaseService\DatabaseControllers\Models\Profile", inversedBy="art")
     * @JoinColumn(name="profile_id", referencedColumnName="id")
     */
    protected $profile_id;

    /**
     * @Column(type="string")
     */
    protected $type;

    /**
     * @Column(type="string", nullable=true, name="original_url")
     */
    protected $originalUrl = null;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $title = null;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $description = null;

    /**
     * @column(type="boolean", nullable=true)
     */
    protected $deleted = false;

    /**
     * @var ArrayCollection
     * @OneToMany(targetEntity="Track", mappedBy="art")
     */
    private $track;

    /**
     * @var ArrayCollection
     * @OneToMany(targetEntity="Album", mappedBy="art")
     */
    private $album;

    /**
     * @var ArrayCollection
     * @OneToMany(targetEntity="Image", mappedBy="art")
     */
    private $image;

    /**
     * @var ArrayCollection
     * @OneToMany(targetEntity="Profile", mappedBy="art")
     */
    private $profile;

    public function __construct()
    {
        $this->track = new ArrayCollection();
        $this->album = new ArrayCollection();
        $this->profile = new ArrayCollection();
        $this->image = new ArrayCollection();
    }
}