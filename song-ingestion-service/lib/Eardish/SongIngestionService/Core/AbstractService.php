<?php
namespace Eardish\SongIngestionService\Core;

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

    /*
 * track bucket
 */
    protected $bucket;

    /**
     * region
     */
    protected $region;

    protected $accessKey;

    protected $secretKey;

    protected $transcoderURL;

    protected $transcoderPort;

    /**
     * sets up connection for remote service
     *
     * protected data members are set up in
     * the implementing service subclass
     *
     * @param $connection
     * @codeCoverageIgnore
     *
     */
    public function __construct(Connection $connection, $config)
    {
        $this->addr = $config->get('notation.address');
        $this->port = $config->get('notation.port');
        $this->dns = $config->get('dns');
        $this->bucket = $config->get('songingestion.aws.bucket');
        $this->region = $config->get('songingestion.aws.region');
        $this->transcoderURL = $config->get('songingestion.xcoder.address');
        $this->transcoderPort = $config->get('songingestion.xcoder.port');
        $this->region = $config->get('songingestion.aws.region');
        $secretData = file_get_contents("/eda/secret/aws.json");
        $secretConfig = json_decode($secretData, true);
        $this->accessKey = $secretConfig['songingestion']['id'];
        $this->secretKey = $secretConfig['songingestion']['key'];
        $this->conn = $connection;
        $this->conn->start($this->addr, $this->port, $this->dns);
    }

    public function generateConfigArray($function, $operation)
    {
        $config['request'] = $function;
        $config['priority'] = $this->getPriority();
        $config['service'] = "SongIngestionService";
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
