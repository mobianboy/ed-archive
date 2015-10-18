<?php
namespace Eardish\DatabaseService\DatabaseControllers\Models;

use Doctrine\ORM\Mapping;
use Eardish\DatabaseService\DatabaseControllers\Models\Traits\Timestamp;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * Class Contact
 *
 * @package Eardish\DatabaseService\DatabaseControllers\Models
 * @Entity @Table(name="contact")
 */
class Contact
{
    use Timestamp;

    /**
     * @Id
     * @Column(type="integer", unique=true)
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $phone = null;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $address1 = null;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $address2 = null;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $city = null;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $state = null;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $zipcode = null;

    /**
     * @var ArrayCollection
     * @OneToMany(targetEntity="Track", mappedBy="profile")
     */
    protected $profile_contact;

    public function __construct()
    {
        $this->profile_contact = new ArrayCollection();
    }
}