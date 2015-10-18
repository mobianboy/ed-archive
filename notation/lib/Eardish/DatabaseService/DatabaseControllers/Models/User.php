<?php
namespace Eardish\DatabaseService\DatabaseControllers\Models;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping;
use Eardish\DatabaseService\DatabaseControllers\Models\Traits\Timestamp;

/**
 * Class Group
 *
 * @package Eardish\DatabaseService\DatabaseControllers\Models
 * @Entity @Table(name="user")
 *
 */
class User
{
    use Timestamp;

    /**
     * @Id
     * @Column(type="integer", unique=true)
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @Column(type="string", unique=true)
     **/
    protected $email;

    /**
     * @Column(type="string")
     */
    protected $password;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $reset_passcode = null;

    /**
     * @Column(type="datetime", nullable=true)
     */
    protected $reset_passcode_exp = null;

    /**
     * @Column(type="integer", nullable=true)
     */
    protected $extra_invites = null;

    /**
     * @var ArrayCollection
     * @OneToOne(targetEntity="Invite", mappedBy="user")
     */
    private $inviter_id;

    /**
     * @var ArrayCollection
     * @OneToOne(targetEntity="Invite", mappedBy="user")
     */
    private $invitee_id;

    /**
     * @var ArrayCollection
     * @OneToMany(targetEntity="Analytic", mappedBy="user")
     */
    private $analytic_users;

    /**
     * @column(type="boolean", nullable=true)
     */
    protected $deleted = false;

    function __construct()
    {
        $this->inviter_id = new ArrayCollection();
        $this->invitee_id = new ArrayCollection();
        $this->analytic_users = new ArrayCollection();
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
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getExtraInvites()
    {
        return $this->extra_invites;
    }

    /**
     * @param mixed $extra_invites
     */
    public function setExtraInvites($extra_invites)
    {
        $this->extra_invites = $extra_invites;
    }

    /**
     * @return ArrayCollection
     */
    public function getInviterId()
    {
        return $this->inviter_id;
    }

    /**
     * @param ArrayCollection $inviter_id
     */
    public function setInviterId($inviter_id)
    {
        $this->inviter_id = $inviter_id;
    }

    /**
     * @return ArrayCollection
     */
    public function getInviteeId()
    {
        return $this->invitee_id;
    }

    /**
     * @param ArrayCollection $invitee_id
     */
    public function setInviteeId($invitee_id)
    {
        $this->invitee_id = $invitee_id;
    }

    /**
     * @return ArrayCollection
     */
    public function getAnalyticUsers()
    {
        return $this->analytic_users;
    }

    /**
     * @param ArrayCollection $analytic_users
     */
    public function setAnalyticUsers($analytic_users)
    {
        $this->analytic_users = $analytic_users;
    }

    /**
     * @return mixed
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param mixed $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

}
