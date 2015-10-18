<?php
namespace Eardish\Gateway;

use Eardish\DataObjects\Blocks\RouteBlock;
use Eardish\Exceptions\EDInvalidOrMissingParameterException;
use Eardish\Gateway;
use Eardish\DataObjects\Request;
use Eardish\DataObjects\Blocks\AuthBlock;
use Eardish\DataObjects\Blocks\DataBlock;
use Eardish\DataObjects\Blocks\ActionBlock;
use Eardish\DataObjects\Blocks\MetaBlock;
use Eardish\Exceptions\EDMissingCredentialsException;
use Monolog\Logger;

class Interpreter
{
    protected $data;
    protected $dataArr;
    protected $router;
    protected $config;
    protected $validator;
    protected $connId;

    /**
     * @var Logger
     */
    protected $logger;

    public function __construct($data, $connId, $config, $routes, $logger)
    {
        $this->router = new Router($routes);
        $this->config = $config;
        $this->connId = $connId;
        $this->data = $data;
        $this->validator = new Validation();
        $this->logger = $logger;
    }

    /**
     * @param $blocks
     * @return bool
     * @throws EDInvalidOrMissingParameterException
     */
    public function validateBlocks($blocks)
    {
        foreach ($blocks as $block) {
            $config = $this->config[$block];
            $clientBlock = $this->data[$block];
            foreach ($config as $key => $params) {
                if ($block == "analytics") {
                    continue;
                }
                if ($params['required']) {
                    if (array_key_exists($key, $clientBlock) || !empty($clientBlock[$key])) {
                        if (!$this->validator->validate($clientBlock[$key], $params['type'])) {
                            throw new EDInvalidOrMissingParameterException("GATEWAY::invalid client data for given route. Required parameter: " . $key . " must be a(n) ". $params['type']);
                        }
                    } else {
                        throw new EDInvalidOrMissingParameterException("GATEWAY::invalid client data for given route. Parameter: " . $key . " is required in the " . $clientBlock);
                    }
                } else {
                    if (array_key_exists($key, $clientBlock) && !empty($clientBlock[$key])) {
                        if (!$this->validator->validate($clientBlock[$key], $params['type'])) {
                            throw new EDInvalidOrMissingParameterException("GATEWAY::invalid client data for given route. Optional parameter: " . $key . " must be a(n) ". $params['type']);
                        }
                    }
                }
            }
        }

        return true;
    }

    /**
     * @param array $routeData
     * @param $conn
     * @return bool
     * @throws EDInvalidOrMissingParameterException
     * @throws EDMissingCredentialsException
     */
    public function validateRouteData(array $routeData, $conn)
    {

        foreach ($routeData as $block => $data) {
            if ($block == 'auth') {
                if(!$conn->isConnAuthed()) {
                    throw new EDMissingCredentialsException("GATEWAY::route requires authentication");
                }
            } else {
                $clientBlock = $this->data[$block];
                foreach ($data as $key => $values) {
                    $this->processData($key, $values, $clientBlock);
                }
            }
        }
        return true;
    }

    /**
     * @param $key
     * @param $validationRules
     * @param $clientBlock
     */
    private function processData($key, $validationRules, $clientBlock)
    {
        $clientValues = $clientBlock[$key];
        if (is_array($clientValues)) {
            if (!is_array($validationRules)) {
                throw new EDInvalidOrMissingParameterException("GATEWAY::invalid client data for given route");
            }
            foreach ($clientValues as $cliKey => $values) {
                // run recursion if array of arrays is given, it will keep doing this until it gets to values that aren't arrays, then it will validate them

                if (is_array($values)) {
                    $this->processData($cliKey, $validationRules, $values);
                } else {
                    foreach ($validationRules as $validationRule) {
                        if (strpos($validationRule, "int") !== false) {
                            $values = intval($values);
                        }
                        if(!$this->validator->validate($values, $validationRule)) {
                            throw new EDInvalidOrMissingParameterException("GATEWAY::invalid client data for given route. Parameter: " . $key . " must be a(n) ". $validationRule);
                        }
                    }
                }
            }
        } else {
            if (!$this->validator->validate($clientValues, $validationRules)) {
                throw new EDInvalidOrMissingParameterException("GATEWAY::invalid client data for given route. Parameter: " . $key . " must be a(n) ". $validationRules);
            }
        }
    }

    /**
     * @return Request
     */
    public function getDataObjects()
    {
        //changes here affect json blocks in ClientData, RequestObjects in DataObjects repo, InterpreterTest,
        // and Request and RequestTest in DataObjects repo
        // TODO refactor! This can be done more intelligently, but focus right now is only to get it working.
        if (array_key_exists('analytics', $this->data)) {
            $params[] = new ActionBlock('analytic/submit/event', 10, $this->data['action']['responseToken']);
            $params[] = new DataBlock($this->data['analytics']);
        } else {
            if (array_key_exists('action', $this->data)) {
                $params[] = new ActionBlock($this->data['action']['route'], $this->data['action']['priority']);
            }

            if (array_key_exists('data', $this->data)) {
                $params[] = new DataBlock($this->data['data']);
            }

            if (array_key_exists('auth', $this->data)) {
                $params[] = new AuthBlock($this->data['auth']['email'], $this->data['auth']['password']);
            }

            if (array_key_exists('meta', $this->data)) {
                $params[] = new MetaBlock($this->data['meta'][''], $this->connId);
            }
        }

        // TODO update response token array key if client side implements differently.

        $params[] = new RouteBlock();

        $request = new Request($params);

        return $request;
    }

    // grabs a specific thing from the objects. example input: "action.route" or "action.priority"
    public function getVal($name, $json = false)
    {
        $val = $this->data;
        if (strpos($name, '.')) {
            $params = explode('.', $name);
            foreach ($params as $param) {
                $val = $val[$param];
            }
            if ($json != false) {
                return json_encode($val);
            }

            return $val;
        } else {

            return json_encode($val[$name]);
        }
    }

    /**
     * @return Router
     */
    public function getRouter()
    {
        return $this->router;
    }
    public function setRouter($router)
    {
        $this->router = $router;
    }

    public function __destruct()
    {
        unset($this->router);
    }
}