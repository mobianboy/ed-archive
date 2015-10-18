<?php
namespace Eardish\ImageProcessingService;

use Monolog\Logger;
use Symfony\Component\Config\Definition\Exception\Exception;


class ServiceKernel
{
    /**
     * @var ImageProcessingService
     */
    protected $service;

    /**
     * @var Logger
     */
    protected $log;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var \ReflectionClass
     */
    protected $reflector;

    protected $requests = [];

    public function __construct(Logger $log)
    {
        $this->log = $log;
        $this->reflector = new \ReflectionClass("\\Eardish\\ImageProcessingService\\ImageProcessingService");
    }

    public function setService(ImageProcessingService $service)
    {
        $this->service = $service;

    }

    /**
     * @param $data
     * @param Array
     * @return string
     *
     * Main function. Call the appropriate Service functions from here
     */
    public function inbound($data, $serviceId)
    {
        $data = json_decode($data, true);
        $this->service->setPriority($data['priority']);
        $this->service->setPort('notation.front.port', true);
        $this->service->setAddr('notation.address', true);
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
            $serviceIndex = count($params)-1;
            $setParams[$serviceIndex] = $serviceId;
        }

        $this->init($data, $serviceId);
        $method->invokeArgs($this->service, $setParams);
    }

    public function register($closures = array(), $serviceId)
    {
        $this->requests[$serviceId]['num'] = count($closures);
        $this->requests[$serviceId]['steps'] = $closures;
    }

    public function init($data, $serviceId)
    {
        $this->requests[$serviceId]['currentIndex'] = 0;
        $this->requests[$serviceId]['fqdn'] = $data['fqdn'];
        $this->requests[$serviceId]['requestId'] = $data['requestId'];
        $this->requests[$serviceId]['data'] = array();
    }

    public function setVariable($serviceId, $name, $value)
    {
        $this->requests[$serviceId]['requestVariables'][$name] = $value;
    }

    public function getVariable($serviceId, $name)
    {
        return $this->requests[$serviceId]['requestVariables'][$name];
    }

    public function getRequest($serviceId, $index)
    {
        return $this->requests[$serviceId]['steps'][$index];
    }

    public function first($serviceId)
    {
        $this->next(array('serviceId' => $serviceId));
    }

    public function selfNext($result)
    {
        $serviceId = $result['serviceId'];
        $oldIndex = $this->requests[$serviceId]['currentIndex'];

        $this->requests[$serviceId]['currentIndex']++;
        $newIndex = $this->requests[$serviceId]['currentIndex']++;

        try {
            $closure = $this->getRequest($serviceId, $oldIndex+1);
            $data = $result['data'];
            if (array_key_exists('success', $result)) {
                $data['success'] = $result['success'];
            }
            $this->requests[$serviceId]['data'][$oldIndex - 1] = $data;
            $value = call_user_func($closure, $this->requests[$serviceId], $oldIndex - 1);

            if (($newIndex+1) == $this->requests[$serviceId]['num']) {
                $this->sendResponse($value, $serviceId);
            }
        } catch (\Exception $e) {
            $this->processException($e, $serviceId);
        }
    }

    public function next($result)
    {
        $serviceId = $result['serviceId'];
        $oldIndex = $this->requests[$serviceId]['currentIndex'];
        $newIndex = $oldIndex + 1;
        $value = null;
        try {
            $closure = $this->getRequest($serviceId, $oldIndex);
            if ($oldIndex > 0) {
                $data = $result['data'];
                if (array_key_exists('success', $result)) {
                    $data['success'] = $result['success'];
                }
                $this->requests[$serviceId]['data'][$oldIndex - 1] = $data;
                $value = call_user_func($closure, $this->requests[$serviceId], $oldIndex - 1);
            } else {
                $value = call_user_func($closure);
            }

            if (isset ($value['procedure']) && isset($value['code'])) {
                $procedure = $value['procedure'];
                $code = $value['code'];
                throw new \Exception("Controller did not complete successfully. Failed on: ". $procedure, $code);
            }
            if (isset($this->requests[$serviceId])) {
                $this->requests[$serviceId]['currentIndex']++;
                if ($newIndex == $this->requests[$serviceId]['num']) {
                    $this->sendResponse($value, $serviceId);
                }
            }
        } catch (\Exception $e) {
            $this->processException($e, $serviceId);
        }
    }

    public function sendResponse($value, $serviceId)
    {
        $value['requestId'] = $this->requests[$serviceId]['requestId'];
        $this->service->setPort('bridge.back.port', true);

        // fqdn gets passed in so dont need to do lookup in app.json
        $this->service->setAddr($this->requests[$serviceId]['fqdn']);
        $this->cleanUp($serviceId);
        $this->service->send($value);
    }

    public function cleanUp($serviceId)
    {
        $closureLength = $this->requests[$serviceId]['num'];

        for ($i = 0;$i < $closureLength;$i++) {
            unset($this->requests[$serviceId]['steps'][$i]);
        }

        unset($this->requests[$serviceId]);
    }

    public function processException(\Exception $ex, $serviceId)
    {
        $exceptionArray = array();
        $exceptionArray["exception"]["code"]    = $ex->getCode();
        $exceptionArray["exception"]["message"] = $ex->getMessage();
        $exceptionArray["requestId"] = $this->requests[$serviceId]['requestId'];
        $this->service->setPort('bridge.back.port', true);
        $this->service->setAddr($this->requests[$serviceId]['fqdn']);
        $this->service->send($exceptionArray);
    }
}
