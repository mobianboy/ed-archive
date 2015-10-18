<?php
namespace Eardish\Gateway;

use Eardish\DataObjects\Request;

trait ClosureKernel {

    public $requests = [];

    public function initRequest($requestId, $dto)
    {
        $this->requests[$requestId]['dto'] = $dto;
        $this->requests[$requestId]['data'] = [];
        $this->requests[$requestId]['currentIndex'] = -1;
    }

    public function setRequests($closures = array(), $requestId)
    {
        $this->requests[$requestId]['num'] = count($closures);
        $this->requests[$requestId]['steps'] = $closures;
    }

    public function getRequest($requestId, $index)
    {
        return $this->requests[$requestId]['steps'][$index];
    }

    public function setVariable($requestId, $name, $value)
    {
        $this->requests[$requestId]['requestVariables'][$name] = $value;
    }

    public function getVariable($requestId, $name)
    {
        return $this->requests[$requestId]['requestVariables'][$name];
    }

    public function first($requestId)
    {
        $init = [
            'requestId' => $requestId
        ];

        $this->next($init);
    }

    public function next($result, $continue = false)
    {
        $requestId = $result['requestId'];
        $this->incrementIndex($requestId);
        $oldIndex = $this->requests[$requestId]['currentIndex'];
        $value = null;
        try {
            $closure = $this->getRequest($requestId, $oldIndex);
            if ($oldIndex != 0) {
                $previousIndex = $oldIndex - 1;
                if ($continue) {
                    $this->requests[$requestId]['data'][$previousIndex] = array();
                } else {
                    $this->requests[$requestId]['data'][$previousIndex] = $result['data'];
                }
                $value = call_user_func($closure, $this->requests[$requestId], $previousIndex, $requestId);
            } else {
                $value = call_user_func($closure, $requestId);
            }
            $newIndex = $oldIndex + 1;

            if (!$this->isCleanedUp($requestId)) {
                if ($newIndex == $this->requests[$requestId]['num']) {
                    $this->cleanUp($requestId);
                }
            }

        } catch (\Exception $e) {
            $this->processException($e, $requestId);
        }
    }

    private function incrementIndex($requestId)
    {
        if (!$this->isCleanedUp($requestId)) {
            $this->requests[$requestId]['currentIndex']++;
        }
    }

    public function processException($exception, $requestId)
    {
        /**
         * @var $dto Request
         */
        $dto = $this->requests[$requestId]['dto'];
        $dto->getAuditBlock()->addException($exception);
        $this->cleanUp($requestId);

    }

    public function cleanUp($requestId)
    {
        $closureLength = $this->requests[$requestId]['num'];

        for ($i = 0;$i < $closureLength;$i++) {
            unset($this->requests[$requestId]['steps'][$i]);
        }

        unset($this->requests[$requestId]);
    }

    private function isCleanedUp($requestId)
    {
        if (isset($this->requests[$requestId])) {
            return false;
        } else {
            return true;
        }
    }
}