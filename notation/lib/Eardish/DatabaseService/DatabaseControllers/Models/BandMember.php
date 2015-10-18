<?php
namespace Eardish\DatabaseService\DatabaseControllers\Models;

use Eardish\DatabaseService\DatabaseControllers\Models\Traits\Timestamp;
/**
 * Class BandMember
 *
 * @package Eardish\DatabaseService\DatabaseControllers\Models
 * @Entity @Table(name="band_member")
 */
class BandMember
{
    use Timestamp;

    /**
     * @Id
     * @Column(type="integer", unique=true)
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="Eardish\DatabaseService\DatabaseControllers\Models\Profile", inversedBy="band_member")
     * @JoinColumn(name="group_id", referencedColumnName="id")
     */
    protected $group_id;

    /**
     * @ManyToOne(targetEntity="Eardish\DatabaseService\DatabaseControllers\Models\Profile", inversedBy="band_member")
     * @JoinColumn(name="member_id", referencedColumnName="id")
     */
    protected $member_id;

    /**
     * @Column(type="string")
     */
    protected $role;

    /**
     * @Column(type="boolean")
     */
    protected $admin;
}