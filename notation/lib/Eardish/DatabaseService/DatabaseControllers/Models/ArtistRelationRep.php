<?php
namespace Eardish\DatabaseService\DatabaseControllers\Models;

use Eardish\DatabaseService\DatabaseControllers\Models\Traits\Timestamp;
use Doctrine\ORM\Mapping;

/**
 * Class Art
 *
 * @package Eardish\DatabaseService\DatabaseControllers\Models
 * @Entity @Table(name="ar_rep")
 */
class ArtistRelationRep
{
    use Timestamp;
    /**
     * @Id
     * @Column(type="integer", unique=true)
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="Eardish\DatabaseService\DatabaseControllers\Models\Profile", inversedBy="ar_rep")
     * @JoinColumn(name="artist_id", referencedColumnName="id")
     */
    protected $artist_id;

    /**
     * @ManyToOne(targetEntity="Eardish\DatabaseService\DatabaseControllers\Models\Profile", inversedBy="ar_rep")
     * @JoinColumn(name="rep_id", referencedColumnName="id")
     */
    protected $rep_id = null;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $ar_rep = null;
}