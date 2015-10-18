<?php
namespace Eardish\EchoService\Core;

abstract class AbstractService
{
    /**
     * @var string
     */
    protected $addr;

    /**
     * @var string
     */
    protected $port;

    /**
     * @var Connection
     */
    protected $conn;

    protected $priority;

    /**
     * sets up connection for remote service
     *
     * protected data members are set up in
     * the implementing service subclass
     *
     * @param $agentConfig
     * @param $connection
     * @codeCoverageIgnore
     *
     */
    public function __construct(Connection $connection, $agentConfig)
    {
        $this->addr = $agentConfig->get('agents.db.address');
        $this->port = $agentConfig->get('agents.db.port');

        $this->conn = $connection;
        $this->conn->start($this->addr, $this->port);
    }

    public function generateConfigArray($function, $operation)
    {
        $config['request'] = $function;
        $config['priority'] = $this->getPriority();
        $config['service'] = "EchoService";
        $config['operation'] = $operation;

        return $config;
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
