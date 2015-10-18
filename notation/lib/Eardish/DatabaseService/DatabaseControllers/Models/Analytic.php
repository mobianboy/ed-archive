<?php
namespace Eardish\DatabaseService\DatabaseControllers\Models;

use Doctrine\ORM\Mapping;

/**
 * Class Analytic
 *
 * @package Eardish\DatabaseService\DatabaseControllers\Models
 * @Entity @Table(name="analytic")
 */
class Analytic {
    /**
     * @Id
     * @Column(type="integer", unique=true)
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $device_type = null;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $device_make = null;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $device_model = null;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $device_carrier = null;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $device_os = null;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $client_version = null;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $device_uuid = null;

    /**
     * @Column(type="integer", nullable=true)
     */
    protected $latitude = null;

    /**
     * @Column(type="integer", nullable=true)
     */
    protected $longitude = null;

    /**
     * @Column(type="datetime", nullable=true)
     */
    protected $time = null;

    /**
     * @ManyToOne(targetEntity="Eardish\DatabaseService\DatabaseControllers\Models\User", inversedBy="analytic")
     * @JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user_id = null;

    /**
     * @ManyToOne(targetEntity="Eardish\DatabaseService\DatabaseControllers\Models\Profile", inversedBy="analytic")
     * @JoinColumn(name="profile_id", referencedColumnName="id")
     */
    protected $profile_id = null;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $view_route = null;

    /**
     * @ManyToOne(targetEntity="Eardish\DatabaseService\DatabaseControllers\Models\Track", inversedBy="analytic")
     * @JoinColumn(name="track_id", referencedColumnName="id")
     */
    protected $track_id = null;

    /**
     * @ManyToOne(targetEntity="Eardish\DatabaseService\DatabaseControllers\Models\Profile", inversedBy="analytic")
     * @JoinColumn(name="artist_id", referencedColumnName="id")
     */
    protected $artist_id = null;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $track_timecode = null;

    /**
     * @Column(type="text", nullable=true)
     */
    protected $player_state = null;

    /**
     * @Column(type="integer", nullable=true)
     */
    protected $session_duration = null;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $event_type = null;

    /**
     * @Column(type="text", nullable=true)
     */
    protected $values = null;

    /**
     * @Column(type="datetime", nullable=true)
     */
    protected $date_created = null;

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
    public function getDeviceType()
    {
        return $this->device_type;
    }

    /**
     * @param mixed $device_type
     */
    public function setDeviceType($device_type)
    {
        $this->device_type = $device_type;
    }

    /**
     * @return mixed
     */
    public function getDeviceMake()
    {
        return $this->device_make;
    }

    /**
     * @param mixed $device_make
     */
    public function setDeviceMake($device_make)
    {
        $this->device_make = $device_make;
    }

    /**
     * @return mixed
     */
    public function getDeviceModel()
    {
        return $this->device_model;
    }

    /**
     * @param mixed $device_model
     */
    public function setDeviceModel($device_model)
    {
        $this->device_model = $device_model;
    }

    /**
     * @return mixed
     */
    public function getDeviceCarrier()
    {
        return $this->device_carrier;
    }

    /**
     * @param mixed $device_carrier
     */
    public function setDeviceCarrier($device_carrier)
    {
        $this->device_carrier = $device_carrier;
    }

    /**
     * @return mixed
     */
    public function getDeviceOs()
    {
        return $this->device_os;
    }

    /**
     * @param mixed $device_os
     */
    public function setDeviceOs($device_os)
    {
        $this->device_os = $device_os;
    }

    /**
     * @return mixed
     */
    public function getClientVersion()
    {
        return $this->client_version;
    }

    /**
     * @param mixed $client_version
     */
    public function setClientVersion($client_version)
    {
        $this->client_version = $client_version;
    }

    /**
     * @return mixed
     */
    public function getDeviceUuid()
    {
        return $this->device_uuid;
    }

    /**
     * @param mixed $device_uuid
     */
    public function setDeviceUuid($device_uuid)
    {
        $this->device_uuid = $device_uuid;
    }

    /**
     * @return mixed
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param mixed $latitude
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    /**
     * @return mixed
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param mixed $longitude
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }

    /**
     * @return mixed
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param mixed $time
     */
    public function setTime($time)
    {
        $this->time = $time;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param mixed $user_id
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * @return mixed
     */
    public function getViewRoute()
    {
        return $this->view_route;
    }

    /**
     * @param mixed $view_route
     */
    public function setViewRoute($view_route)
    {
        $this->view_route = $view_route;
    }

    /**
     * @return mixed
     */
    public function getTrackId()
    {
        return $this->track_id;
    }

    /**
     * @param mixed $track_id
     */
    public function setTrackId($track_id)
    {
        $this->track_id = $track_id;
    }

    /**
     * @return mixed
     */
    public function getTrackTimecode()
    {
        return $this->track_timecode;
    }

    /**
     * @param mixed $track_timecode
     */
    public function setTrackTimecode($track_timecode)
    {
        $this->track_timecode = $track_timecode;
    }

    /**
     * @return mixed
     */
    public function getSessionDuration()
    {
        return $this->session_duration;
    }

    /**
     * @param mixed $session_duration
     */
    public function setSessionDuration($session_duration)
    {
        $this->session_duration = $session_duration;
    }

    /**
     * @return mixed
     */
    public function getEventType()
    {
        return $this->event_type;
    }

    /**
     * @param mixed $event_type
     */
    public function setEventType($event_type)
    {
        $this->event_type = $event_type;
    }

    /**
     * @return mixed
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @param mixed $values
     */
    public function setValues($values)
    {
        $this->values = $values;
    }
}



