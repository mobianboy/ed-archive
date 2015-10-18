<?php
namespace Eardish\DatabaseService\DatabaseControllers\Models;

use Doctrine\ORM\Mapping;
use Eardish\DatabaseService\DatabaseControllers\Models\Traits\Timestamp;
/**
 * Class Image
 *
 * @package Eardish\DatabaseService\DatabaseControllers\Models
 * @Entity @Table(name="image")
 */
class Image
{
    use Timestamp;
    /**
     * @Id
     * @Column(type="integer", unique=true)
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="Eardish\DatabaseService\DatabaseControllers\Models\Art", inversedBy="image")
     * @JoinColumn(name="art_id", referencedColumnName="id")
     */
    protected $art;

    /**
     * @Column(type="string")
     */
    protected $format;

    /**
     * @Column(type="string")
     */
    protected $url;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $relative_url;
}
