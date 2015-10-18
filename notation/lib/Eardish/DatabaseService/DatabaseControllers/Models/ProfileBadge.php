<?php
namespace Eardish\DatabaseService\DatabaseControllers\Models;

use Doctrine\ORM\Mapping;
use Eardish\DatabaseService\DatabaseControllers\Models\Traits\Timestamp;
/**
 * Class ProfileBadge
 *
 * @package Eardish\DatabaseService\DatabaseControllers\Models
 * @Entity @Table(name="profile_badge")
 */
class ProfileBadge
{
    use Timestamp;
    /**
     * @Id
     * @Column(type="integer", unique=true)
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="Eardish\DatabaseService\DatabaseControllers\Models\Profile", inversedBy="profile_badge")
     * @JoinColumn(name="profile_id", referencedColumnName="id")
     */
    protected $profile_id;

    /**
     * @ManyToOne(targetEntity="Eardish\DatabaseService\DatabaseControllers\Models\Badge", inversedBy="profile_badge")
     * @JoinColumn(name="badge_id", referencedColumnName="id")
     */
    protected $badge_id;

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
    public function getProfileId()
    {
        return $this->profile_id;
    }

    /**
     * @param mixed $profile_id
     */
    public function setProfileId($profile_id)
    {
        $this->profile_id = $profile_id;
    }

    /**
     * @return mixed
     */
    public function getBadgeId()
    {
        return $this->badge_id;
    }

    /**
     * @param mixed $badge_id
     */
    public function setBadgeId($badge_id)
    {
        $this->badge_id = $badge_id;
    }

    /**
     * @return mixed
     */
    public function getDateAcquired()
    {
        return $this->date_acquired;
    }

    /**
     * @param mixed $date_acquired
     */
    public function setDateAcquired($date_acquired)
    {
        $this->date_acquired = $date_acquired;
    }

}
