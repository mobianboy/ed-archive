<?php

class Job
{

    protected $resultCode;
    protected $task;
    protected $channel;
    protected $slot;

    function __construct($task, $channel, $timeSlot)
    {
        $this->resultCode = null;
        $this->task = $task;
        $this->channel = $channel;
        $this->slot = $timeSlot
    }

    function getTask()
    {
        return $this->task;
    }

    function getChannel()
    {
        return $this->channel;
    }

    function getSlot()
    {
        return $this->slot;
    }

    function setResult($result)
    {
        $this->resultCode = $result;
    }

    function getResult()
    {
        return $this->resultCode;
    }

}