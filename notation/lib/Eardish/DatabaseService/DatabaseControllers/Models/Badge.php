<?php
namespace Eardish\DatabaseService\DatabaseControllers\Models;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping;
use Eardish\DatabaseService\DatabaseControllers\Models\Traits\Timestamp;
/**
 * Class Badge
 *
 * @package Eardish\DatabaseService\DatabaseControllers\Models
 * @Entity @Table(name="badge")
 */
class Badge
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
    protected $name;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $description;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $type;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $awarded_to;

    /**
     * @var ArrayCollection
     * @OneToMany(targetEntity="ProfileBadge", mappedBy="badge")
     */
    protected $profile_badges;

    public function __construct()
    {
        $this->profile_badges = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getBadgeName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setBadgeName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getBadgeType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setBadgeType($type)
    {
        $this->type = $type;
    }

    /**
     * @return ArrayCollection
     */
    public function getProfileBadges()
    {
        return $this->profile_badges;
    }

    /**
     * @param ArrayCollection $profile_badges
     */
    public function setProfileBadges($profile_badges)
    {
        $this->profile_badges = $profile_badges;
    }
}
