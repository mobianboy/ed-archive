<?php
namespace Eardish\Bridge;

/**
 * Class ServiceManager
 *
 *          This class, when instantiated, provides an external interface for the services
 *          required by the eardish application.
 */
use Eardish\AppConfig;
use Eardish\Bridge\Agents\Core\GatewayConnection;
use Eardish\Bridge\Agents\Core\JobsConnection;
use Eardish\Bridge\Agents\GatewayAgent;
use Eardish\DataObjects\Request;
use Eardish\DataObjects\Blocks\DataBlock;
use Eardish\DataObjects\Blocks\MetaBlock;
use Eardish\DataObjects\Response;
use Eardish\Bridge\Agents\Core\Connection;
use Aura\Di\Container;
use Aura\Di\Factory;
use Eardish\Bridge\Config\JSONLoader;
use Monolog\Logger;

class BridgeKernel
{
    protected $auraContainer;
    protected $priority;
    protected $agents;
    protected $requests;

    /**
    * @var Logger
    */
    protected $logger;

    public function __construct(Connection $connection, AppConfig $agents, $logger)
    {
        $this->agents = $agents;
        $this->auraContainer = new Container(new Factory());
        $this->auraContainer->setAutoResolve(false);
        $this->initDIC($connection);
        $this->logger = $logger;

        $this->logger->addInfo('Bridge Logger is ready');
    }

    /**
     * marshals the data from a network-friendly format to
     * program readable DTO objects
     *
     * @param $raw - data to be marshaled
     * unserialized DTO ready for consumption
     * @return Request
     */
    public function unserialize($raw)
    {
        return unserialize(utf8_decode($raw));
    }

    /**
     * receives DTOs, pulls the controller information needed
     * to load the proper one and calls its action
     *
     * @param $dto
     * @return mixed
     */
    public function inbound(Request $dto, $requestId)
    {
        $this->priority = $dto->getActionBlock()->getPriority();
        $controllerName = $dto->getRouteBlock()->getControllerName();
        $controllerPath = "Eardish\\Bridge\\Controllers\\".$controllerName."Controller";
        $method = $dto->getRouteBlock()->getControllerMethod();
        // make loadController call (to load proper controller - route, action, request object)
        $controller = $this->auraContainer->newInstance($controllerPath);

        $controller->setKernel($this);

        $this->initRequest($requestId, $dto);

		$this->logger->addInfo('Inbound request:', [
            'priority' => $this->priority,
            'controllerName' => $controllerName,
            'controllerPath' => $controllerPath,
            'method' => $method
        ]);

        if ($dto->blockExists('data')) {
            $controller->setDataBlock($dto->getDataBlock()->getDataArray());
        }

        $controller->setMetaBlock($dto->getMetaBlock());

        $controller->$method($requestId);
    }

    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * takes the result from the controller-action
     * do any optional work
     * then connect to APIManager/server
     * .. send data
     * .. disconnect
     * done
     *
     * @codeCoverageIgnore
     * @param $dto Request
     * @param $result
     * @return int
     */
    public function outbound($dto, $result = null)
    {
        /**
        * @var MetaBlock
        */
        $metaBlock = $dto->getMetaBlock();
        // move request token from ActionBlock to MetaBlock if it exists
        if($dto->getActionBlock()->getResponseToken()) {
            $metaBlock->setResponseToken($dto->getActionBlock()->getResponseToken());

            $exists = strpos($metaBlock->getResponseToken(), "cron");
            if ($exists !== false) {
                $connection = new JobsConnection();
                $connection->start("localhost", 7082);
                if ($result['data']['success']) {
                    $connection->send(['success' => true]);
                    return true;
                } else {
                    $connection->send(['success' => false]);
                    return false;
                }
            }
        }
        if ($dto->getAuditBlock()->hasExceptions()) {
            $response = new Response(array($dto->getAuditBlock(), $metaBlock));
        } else {
            if(isset($result['modelType'])) {
                $metaBlock->setModelType($result['modelType']);
            }
            if(isset($result['listType'])) {
                $metaBlock->setListType($result['listType']);
            }
            if (!empty($result['data'])) {
                $response = new Response(array(new DataBlock($result['data']), $dto->getAuditBlock(), $metaBlock));
            } else {
                $response = new Response(array($dto->getAuditBlock(), $metaBlock));
            }
        }
        $connection = new GatewayConnection();
        $gatewayAgent = new GatewayAgent($connection, 10, (array(
            'address' => $response->getMetaBlock()->getFqdn(),
            'port' => $this->agents->get('gateway.service-port'),
            'dns' => $this->agents->get('dns'),
            'fqdn' => $dto->getMetaBlock()->getFqdn()
        )));
        $this->sendServiceResult($response, $gatewayAgent);

        $this->logger->addInfo('Outbound Message has been sent');
    }

    /**
     * connects to the APIManager's server for pass two (blocking socket connect)
     * passes the result
     * returns a status  -- throws exceptions as needed
     * disconnects
     *
     * @param $response - the result object from running the service
     * @param $connection SocketAgent()
     * @return mixed $status - a status code for success (zero) or fail (non-zero)
     */
    public function sendServiceResult($response, GatewayAgent $connection)
    {
        $this->logger->addInfo('Sending result to Gateway for second pass');
        $connection->sendToGateway($response);
    }

    /**
     * Automatically loads dependencies for controllers defined in Controllers.json file.
     *
     * @param $connection
     * @throws \Aura\Di\Exception\ContainerLocked
     * @throws \Aura\Di\Exception\ServiceNotObject
     */
    public function initDIC(Connection $connection)
    {
        // Load Controllers from JSON file
        $json = new JsonLoader();
        $jsonControllers = $json->loadJSONConfig('lib/Eardish/Bridge/Controllers/Core/Controllers.json');
        $controllers = $jsonControllers['Controllers'];

        foreach ($controllers as $controller) {
            // Create a reflection of each controller and pull out it's dependencies
            $controllerPath = "Eardish\\Bridge\\Controllers\\".$controller."Controller";
            $controllerReflection = new \ReflectionClass($controllerPath);
            $arguments = $controllerReflection->getConstructor()->getParameters();

            foreach ($arguments as $arg) {
                $agentNamespace = $arg->getClass()->getName();
                $parts = explode("\\", $agentNamespace);
                $agentName = array_pop($parts);

                $agentKeyName = strtolower(str_replace('Agent', '', $agentName));
                // For each dependency, add it to the aura Agent if it doesn't already exist.
                if (!array_key_exists($agentName, $this->auraContainer->params)) {
                    $this->auraContainer->set($agentName, $this->auraContainer->lazyNew(
                        $agentNamespace,
                        array(
                            'connection' => clone $connection,
                            'priority' =>  $this->getPriority(),
                            'agent' => [
                                'address' => $this->agents->get($agentKeyName.'.address'),
                                'port' => $this->agents->get($agentKeyName.'.front.port'),
                                'dns' => $this->agents->get('dns'),
                                'fqdn' => $this->agents->get('fqdn')
                            ]
                        )
                    ));
                }
                // Point the controllers dependency to the dependency we just added to the Agent
                $this->auraContainer->params[$controllerPath][$arg->getName()] = $this->auraContainer->lazyGet($agentName);
            }
            // Add the actual Controller to the aura Agent once all its dependencies are defined.
            $this->auraContainer->set($controllerPath, $this->auraContainer->lazyNew($controllerReflection->getName()));
        }
    }

    public function getDic()
    {
        return $this->auraContainer;
    }

    /**
     * @param $requestId
     * @param $dto Request
     */
    public function initRequest($requestId, $dto)
    {
        $this->requests[$requestId]['dto'] = $dto;
        $this->requests[$requestId]['data'] = [];
        $this->requests[$requestId]['currentIndex'] = -1;
        $this->requests[$requestId]['method'] = $dto->getRouteBlock()->getControllerMethod();
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
                    $this->outbound($this->requests[$requestId]['dto'], $value);
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
        $this->outbound($dto);
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
