<?php

class Session
{
    /**
     * @var string
     */
    protected $name;
    /**
     * @var string
     */
    protected $desc;

    /**
     * @var array of Tasks
     */
    protected $tasks;

    /**
     * @var array of Channels
     */
    protected $channels;

    /**
     * @var integer
     */
    protected $currentTaskStep;

    function __construct(array $test)
    {
        $this->session = $test["session"];
        $this->currentSendStep = 0;
    }

    function getName() { return $this->name;}
    function setName($name) { $this->name = $name;}

    function getDesc() {return $this->desc;}
    function setDesc($desc) { $this->desc = $desc;}

    function getTaskArray() {return $this->tasks;}
    function getNextTask() {return $this->tasks[$this->currentTaskStep++];}
    function rewindTasks() {$this->currentTaskStep = 0;}



}