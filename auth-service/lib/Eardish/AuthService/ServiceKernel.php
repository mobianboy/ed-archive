<?php
namespace Eardish\AuthService;

use Monolog\Logger;

class ServiceKernel
{
    /**
     * @var AuthService
     */
    protected $service;

    /**
     * @var Logger
     */
    protected $log;
    protected $requests = [];
    protected $reflector;

    public function __construct(Logger $log)
    {
        $this->log = $log;
        $this->reflector = new \ReflectionClass("Eardish\\AuthService\\AuthService");
    }


    public function setService(AuthService $service)
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
        $this->requests[$serviceId]['currentIndex'] = -1;
        $this->requests[$serviceId]['fqdn'] = $data['fqdn'];
        $this->requests[$serviceId]['method'] = $data['method'];
        $this->requests[$serviceId]['requestId'] = $data['requestId'];
        $this->requests[$serviceId]['data'] = array();
    }

    public function getRequest($serviceId, $index)
    {
        return $this->requests[$serviceId]['steps'][$index];
    }

    public function first($serviceId)
    {
        $this->next(array('serviceId' => $serviceId));
    }
    public function next($result, $continue = false)
    {
        $serviceId = $result['serviceId'];
        $this->incrementIndex($serviceId);
        $oldIndex = $this->requests[$serviceId]['currentIndex'];
        $value = null;
        try {
            $closure = $this->getRequest($serviceId, $oldIndex);
            if ($oldIndex != 0) {
                $previousIndex = $oldIndex - 1;
                if ($continue) {
                    $this->requests[$serviceId]['data'][$previousIndex] = array();
                } else {
                    $this->requests[$serviceId]['data'][$previousIndex] = $result['data'];
                }
                $value = call_user_func($closure, $this->requests[$serviceId], $previousIndex, $serviceId);
            } else {
                $value = call_user_func($closure, $serviceId);
            }
            $newIndex = $oldIndex + 1;

            if (!$this->isCleanedUp($serviceId)) {
                if ($newIndex == $this->requests[$serviceId]['num']) {
                    $value['requestId'] = $this->requests[$serviceId]['requestId'];
                    $this->setPortToReturnTo($serviceId);
                    // fqdn gets passed in so dont need to do lookup in app.json
                    $this->service->setAddr($this->requests[$serviceId]['fqdn']);
                    $this->cleanUp($serviceId);
                    $this->service->send($value);
                }
            }

        } catch (\Exception $e) {
            $this->processException($e, $serviceId);
        }
    }
    private function incrementIndex($requestId)
    {
        if (!$this->isCleanedUp($requestId)) {
            $this->requests[$requestId]['currentIndex']++;
        }
    }

    private function isCleanedUp($requestId)
    {
        if (isset($this->requests[$requestId])) {
            return false;
        } else {
            return true;
        }
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
        $this->setPortToReturnTo($serviceId);
        $this->service->setAddr($this->requests[$serviceId]['fqdn']);
        $this->service->send($exceptionArray);
    }

    public function setPortToReturnTo($serviceId)
    {
        if ($this->requests[$serviceId]['method'] == 'authenticate') {
            $this->service->setPort('gateway.back-port', true);
        } else {
            $this->service->setPort('bridge.back.port', true);
        }
    }

    public function setVariable($serviceId, $name, $value)
    {
        $this->requests[$serviceId]['requestVariables'][$name] = $value;
    }

    public function getVariable($serviceId, $name)
    {
        return $this->requests[$serviceId]['requestVariables'][$name];
    }
}
