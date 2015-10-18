<?php
namespace Eardish\DatabaseService\DatabaseControllers\Models;

use Doctrine\ORM\Mapping;
use Eardish\DatabaseService\DatabaseControllers\Models\Traits\Timestamp;
/**
 * Class Friend
 *
 * @package Eardish\DatabaseService\DatabaseControllers\Models
 * @Entity @Table(name="friend")
 */
class Friend
{
    use Timestamp;
    /**
     * @Id
     * @Column(type="integer", unique=true)
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="Eardish\DatabaseService\DatabaseControllers\Models\Profile", inversedBy="friend")
     * @JoinColumn(name="profile1_id", referencedColumnName="id")
     */
    protected $profile1;

    /**
     * @ManyToOne(targetEntity="Eardish\DatabaseService\DatabaseControllers\Models\Profile", inversedBy="friend")
     * @JoinColumn(name="profile2_id", referencedColumnName="id")
     */
    protected $profile2;

    public function __construct()
    {
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
    public function getProfile1()
    {
        return $this->profile1;
    }

    /**
     * @param mixed $profile1
     */
    public function setProfile1($profile1)
    {
        $this->profile1 = $profile1;
    }

    /**
     * @return mixed
     */
    public function getProfile2()
    {
        return $this->profile2;
    }

    /**
     * @param mixed $profile2
     */
    public function setProfile2($profile2)
    {
        $this->profile2 = $profile2;
    }

}
