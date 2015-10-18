<?php
namespace Eardish\DatabaseService\DatabaseControllers\Models;

use Doctrine\ORM\Mapping;
use Eardish\DatabaseService\DatabaseControllers\Models\Traits\Timestamp;
/**
 * Class TranscodeJob
 *
 * @package Eardish\DatabaseService\DatabaseControllers\Models
 * @Entity @Table(name="transcode_job")
 */
class TranscodeJob
{
    use Timestamp;
    /**
     * @Id
     * @Column(type="integer", unique=true)
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $id;


    /**
     * @ManyToOne(targetEntity="Eardish\DatabaseService\DatabaseControllers\Models\Track", inversedBy="transcode_job")
     * @JoinColumn(name="track_id", referencedColumnName="id")
     */
    protected $track_id;

    /**
     * @ManyToOne(targetEntity="Eardish\DatabaseService\DatabaseControllers\Models\Audio", inversedBy="transcode_job")
     * @JoinColumn(name="audio_id", referencedColumnName="id")
     */
    protected $audio_id = null;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $source;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $target;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $encoding;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $bitrate;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $started_by;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $started_on;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $finished_on;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $pushed_on;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $status;
}