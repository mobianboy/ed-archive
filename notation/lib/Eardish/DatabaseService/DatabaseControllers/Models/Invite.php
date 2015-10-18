<?php

namespace Eardish\DatabaseService\DatabaseControllers\Models;

use Doctrine\ORM\Mapping;


/**
 * Class Invite
 *
 * @package Eardish\DatabaseService\DatabaseControllers\Models
 * @Entity @Table(name="invite")
 */

class Invite {

    /**
     * @Id
     * @Column(type="integer", unique=true)
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="Eardish\DatabaseService\DatabaseControllers\Models\User", inversedBy="invite")
     * @JoinColumn(name="inviter_id", referencedColumnName="id")
     */
    protected $inviter_id;

    /**
     * Null at first, fill in once code is redeemed with actual new user ID
     *
     * @ManyToOne(targetEntity="Eardish\DatabaseService\DatabaseControllers\Models\User", inversedBy="invite")
     * @JoinColumn(name="invitee_id", referencedColumnName="id")
     */
    protected $invitee_id = null;

    /**
     * @Column(type="string")
     */
    private $invite_code;

    /**
     * @Column(type="string", nullable=true)
     */
    private $invitee_email;

    /**
     * @Column(type="datetime", nullable=true)
     */
    protected $date_issued;

    /**
     * @Column(type="datetime", nullable=true)
     */
    protected $date_redeemed;

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

    public function geInviterId()
    {
        return $this->inviter_id;
    }

    /**
     * @param mixed $inviter_id
     */
    public function setInviterId($inviter_id)
    {
        $this->inviter_id = $inviter_id;
    }

    public function geInviteeId()
    {
        return $this->invitee_id;
    }

    /**
     * @param mixed $invitee_id
     */
    public function setInviteeId($invitee_id)
    {
        $this->invitee_id = $invitee_id;
    }

    /**
     * @return mixed
     */
    public function getInviteCode()
    {
        return $this->invite_code;
    }
    /**
     * @param mixed $invite_code
     */
    public function setInviteCode($invite_code)
    {
        $this->invite_code = $invite_code;
    }

    /**
     * @return mixed
     */
    public function getInviteeEmail()
    {
        return $this->invitee_email;
    }

    /**
     * @param mixed $invitee_email
     */
    public function setInviteeEmail($invitee_email)
    {
        $this->invitee_email = $invitee_email;
    }
}