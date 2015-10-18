<?php
namespace Eardish\Gateway\Agents\Core;

abstract class AbstractAgent
{
    protected $addr;
    protected $port;
    protected $conn;
    protected $priority;
    protected $dns;
    protected $selfAddr;

    /**
     * sets up connection for remote service
     *
     * protected data members are set up in
     * the implementing service subclass
     *
     * @param $agentConfig
     * @param $name
     * @param $connection
     */
    public function __construct(Connection $connection, $agentConfig, $name)
    {
        // Load the config for the Agents using the configuration file.
        $this->addr = $agentConfig->get($name . '.address');
        $this->port = $agentConfig->get($name . '.front.port');
        $this->dns = $agentConfig->get('dns');
        $this->conn = $connection;
        $this->selfAddr = $agentConfig->get('fqdn');

        $this->conn->start($this->addr, $this->port, $this->dns);
    }

    public function arrayGenerator($function, $priority, $requestId)
    {
        return [
            'method' => $function,
            'priority' => $priority,
            'requestId' => $requestId,
            'fqdn' => $this->selfAddr
        ];
    }

    /**
     * after successfully connecting to service
     * pass data and return raw response data
     *
     * @param $data
     * @return string
     */
    protected function send($data)
    {
        $this->conn->send($data);
    }
}
