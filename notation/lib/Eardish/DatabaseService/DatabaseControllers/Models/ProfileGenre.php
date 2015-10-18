<?php
namespace Eardish\DatabaseService\DatabaseControllers\Models;

use Doctrine\ORM\Mapping;
use Eardish\DatabaseService\DatabaseControllers\Models\Traits\Timestamp;

/**
 * Class ProfileGenre
 *
 * @package Eardish\DatabaseService\DatabaseControllers\Models
 * @Entity @Table(name="profile_genre")
 */
class ProfileGenre
{
    use Timestamp;
    /**
     * @Id
     * @Column(type="integer", unique=true)
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="Eardish\DatabaseService\DatabaseControllers\Models\Profile", inversedBy="profile_genre")
     * @JoinColumn(name="profile_id", referencedColumnName="id")
     */
    protected $profile_id;

    /**
     * @ManyToOne(targetEntity="Eardish\DatabaseService\DatabaseControllers\Models\Genre", inversedBy="profile_genre")
     * @JoinColumn(name="genre_id", referencedColumnName="id")
     */
    protected $genre_id;
}