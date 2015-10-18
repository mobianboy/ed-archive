<?php
namespace Eardish\DatabaseService\DatabaseControllers\Models;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping;
use Eardish\DatabaseService\DatabaseControllers\Models\Traits\Timestamp;
/**
 * Class Audio
 *
 * @package Eardish\DatabaseService\DatabaseControllers\Models
 * @Entity @Table(name="audio")
 */
class Audio
{
    use Timestamp;
    /**
     * @Id
     * @Column(type="integer", unique=true)
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="Eardish\DatabaseService\DatabaseControllers\Models\Track", inversedBy="audio")
     * @JoinColumn(name="track_id", referencedColumnName="id")
     */
    protected $track_id;

    /**
     * @Column(type="string")
     */
    protected $format;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $url;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $relative_url;

    /**
     * @Column(type="integer", nullable=true)
     */
    protected $bitrate;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $encoding;

    /**
     * @var ArrayCollection
     * @OneToMany(targetEntity="TranscodeJob", mappedBy="audio")
     */
    private $transcode_job_audio;


    public function __construct()
    {
        $this->transcode_job_audio = new ArrayCollection();
    }
}
