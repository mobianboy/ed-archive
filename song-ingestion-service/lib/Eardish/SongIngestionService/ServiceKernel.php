<?php
namespace Eardish\SongIngestionService;

use Monolog\Logger;

class ServiceKernel
{
    /**
     * @var SongIngestionService
     */
    protected $service;

    /**
     * @var Logger
     */
    protected $log;

    protected $config;

    public function __construct(SongIngestionService $service, Logger $log)
    {
        $this->service = $service;
        $this->log = $log;
        $this->reflector = new \ReflectionClass("\\Eardish\\SongIngestionService\\SongIngestionService");
    }

    /**
     * @param $data string
     * @param Array
     * @return string
     *
     * Main function. Call the appropriate Service functions from here
     */
    public function execute($data)
    {
        $data = json_decode($data, true);

        $this->service->setPriority($data['priority']);
        // pull the action out of the data and prepare data.
        $method = $this->reflector->getMethod($data['method']);
        $params = $method->getParameters();
        $setParams = array();
        if (count($params)) {
            foreach($params as $param) {
                if (isset($data['params'][$param->getName()])) {
                    $setParams[$param->getPosition()] = $data['params'][$param->getName()];
                } elseif (!$param->allowsNull()) {
                    return false;
                } else {
                    $setParams[$param->getPosition()] = null;
                }
            }
        }

        return $method->invokeArgs($this->service, $setParams);
    }

    public function getConfig()
    {
        return $this->config;
    }
}
