<?php
namespace Eardish\MusicService\Core;
use Eardish\MusicService\ServiceKernel;
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
    protected $dns;

    protected $priority;

    protected $agentConfig;
    protected $selfPortBack;
    protected $selfAddr;

    /**
     * @param Connection $connection
     * @param ServiceKernel $serviceKernel
     * @param $agentConfig
     */
    public function __construct(Connection $connection, ServiceKernel $serviceKernel, $agentConfig)
    {
        $this->addr = $agentConfig->get('notation.address');
        $this->port = $agentConfig->get('notation.front.port');
        $this->dns = $agentConfig->get('dns');
        $this->selfAddr = $agentConfig->get('fqdn');
        $this->selfPortBack = $agentConfig->get('music.back.port');
        $this->serviceKernel = $serviceKernel;
        $this->agentConfig = $agentConfig;
        $this->conn = $connection;
    }

    public function generateConfigArray($function, $operation,  $serviceId)
    {
        $config['request'] = $function;
        $config['priority'] = $this->getPriority();
        $config['service'] = "MusicService";
        $config['operation'] = $operation;
        $config['serviceId'] = $serviceId;
        $config['fqdn'] = $this->selfAddr;
        $config['port'] = $this->selfPortBack;

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

    public function setPort($port, $lookup = false)
    {
        if (!$lookup) {
            $this->port = $port;
        } else {
            $this->port = $this->agentConfig->get($port);
        }
    }

    public function setAddr($addr, $lookup = false)
    {
        if (!$lookup) {
            $this->addr = $addr;
        } else {
            $this->addr = $this->agentConfig->get($addr);
        }
    }

    public function send($sendData)
    {
        $this->conn->start($this->addr, $this->port);
        $this->conn->send($sendData);
    }
}