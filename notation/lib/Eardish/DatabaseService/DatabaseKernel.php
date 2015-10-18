<?php
namespace Eardish\DatabaseService;

use Eardish\DatabaseService\Core\Connection;
use Monolog\Logger;

class DatabaseKernel
{
    /**
     * @var DatabaseService
     */
    protected $service;

    /**
     * @var Logger
     */
    protected $log;

    public function __construct(DatabaseService $service, Logger $log)
    {
        $this->service = $service;
        $this->log = $log;
    }

    /**
     * @param $json
     * @return string
     *
     * Main function. Call the appropriate Service functions from here
     */
    public function execute($json)
    {
        // pull the action out of the data and prepare data.
        $data = json_decode($json, true);
        $serviceId = $data['serviceId'];
        $this->log->addInfo('Info Received', $data);
            //Process Analytic Service Events
        $dbResponse =  $this->service->buildQuery($data);
        $dbResponse['serviceId'] = $serviceId;
        $this->sendToService($dbResponse, $data);
    }

    public function sendToService($responseData, $data)
    {
        $connection = new Connection();
        $connection->start($data['fqdn'], $data['port']);
        $connection->sendToService($responseData);
    }

    public function processException(\Exception $ex, $data)
    {
        $exceptionArray = array();
        $exceptionArray["exception"]["code"]    = $ex->getCode();
        $exceptionArray["exception"]["message"] = $ex->getMessage();
        $exceptionArray["serviceId"] = $data['serviceId'];

        $this->sendToService($exceptionArray, $data);
    }
}
