<?php


class Task
{
    protected $taskArray;
    protected $name;
    protected $nextTask;
    protected $config;
    protected $conn;
    protected $rr;
    protected $request;
    protected $response;



    function __construct($taskArray)
    {
        $this->taskArray = $taskArray;
    }

    function getName()
    {
        if (!(isset($this->name))) {
            $this->name = $this->taskArray["name"];
        }
        return $this->name;
    }

    function getNextTask()
    {
        if(!(isset($this->nextTask))) {
            $this->nextTask = $this->taskArray["next-task"];
        }
        return $this->nextTask;
    }

    function getConfig()
    {
        if(!(isset($this->config))) {
            $this->config = $this->taskArray["config"];
        }
        return $this->config;
    }

    function getChannel()
    {
        if(!(isset($this->conn))) {
            $this->conn = $this->getConfig()["channel"];
        }
        return $this->conn;
    }

    function getRRBlock()
    {
        if(!(isset($this->rr))) {
            $this->rr = $this->getConfig()["rr"];
        }
        return $this->rr;
    }

    function getRequestBlock()
    {
        if(!(isset($this->request))) {
            $this->request = $this->getRRBlock()["request"];
        }
        return $this->request;
    }

    function getResponseBlock()
    {
        if(!(isset($this->response))) {
            $this->response = $this->getRRBlock()["response"];
        }
        return $this->response;
    }

    function getCallback()
    {
        if(!(isset($this->callback))) {
            $this->callback = $this->getResponseBlock()["handler"];
        }
    }

    function requestData()
    {
        return file_get_contents($this->getRequestBlock()["read"]);
    }

    function writeReponse($data)
    {
        return file_put_contents($this->getResponseBlock()["write"], $data);
    }
}