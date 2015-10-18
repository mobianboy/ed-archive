<?php
namespace Eardish\Bridge\Agents\Core;

abstract class AbstractAgent
{
    protected $addr;
    protected $port;
    protected $conn;
    protected $priority;
    protected $dns;
    protected $index;
    protected $fqdn;

    /**
     * sets up connection for remote service
     *
     * protected data members are set up in
     * the implementing service subclass
     *
     * @param $connection Connection
     * @param $priority
     * @param $agent array
     * @codeCoverageIgnore
     */
    public function __construct($connection, $priority, array $agent)
    {
        $this->addr = $agent['address'];
        $this->port = $agent['port'];
        $this->fqdn = $agent['fqdn'];
        $this->priority = $priority;
        $this->conn = $connection;
        $this->conn->start($this->addr, $this->port);
    }

    public function arrayGenerator($function, $requestId)
    {
        return [
            'method' => $function,
            'priority' => $this->priority,
            'requestId' => $requestId,
            'fqdn' => $this->fqdn
        ];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getAddr()
    {
        return $this->addr;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getPort()
    {
        return $this->port;
    }

    public function getConn()
    {
        return $this->conn;
    }

    public function setConn($conn)
    {
        $this->conn = $conn;
    }

    /**
     * @return mixed
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param mixed $priority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }
}
