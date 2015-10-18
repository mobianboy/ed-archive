<?php
namespace Eardish\Gateway;

use Eardish\DataObjects\Blocks\MetaBlock;
use Eardish\DataObjects\Blocks\AuditBlock;
use Eardish\DataObjects\Request;
use Eardish\Exceptions\EDConnectionWriteException;
use Eardish\Gateway\Agents\BridgeAgent;
use Eardish\Gateway\config\JSONLoader;
use Eardish\Gateway\Socket\Frames\Framer;
use Eardish\Gateway\Agents\AuthAgent;
use Eardish\Gateway\Agents\AnalyticsAgent;
use Eardish\Gateway\Agents\Core\Connection;
use Eardish\DataObjects\Response;
use Eardish\Exceptions\EDInvalidOrMissingParameterException;
use Eardish\Exceptions\EDTransportException;
use Monolog\Logger;

use \Eardish\Exceptions\EDException;

/** GatewayKernel.php
 *
 *
 *  manages communication between the server script (public facing) and
 *  the internal components of the Eardish core system.
 *
 */
class GatewayKernel
{
    use ClosureKernel;
    protected $connMapper = null;
    protected $logger = null;

    /**
     * @var Builder
     */
    protected $builder;
    /**
     * @var Agents\Core\Connection- (for auth service)
     */
    protected $connection;
    protected $blockConfig = array();
    protected $routeConfig = array();
    protected $routerConnContainer;
    protected $framer;
    protected $host;
    protected $appConfig;

    protected $eventCount = array();
    /**
     * constructor builds out the interpreter instance and fetches
     * data objects.
     *
     * it also fetches an instance of the ConnectionManager and
     * registers the user/connection/location (obtained through
     * the $server instance)
     *
     * @param $appConfig
     * @param $host
     * @param $logger Logger
     */
    public function __construct(Logger $logger, $appConfig, $host)
    {
        $this->host = $host;
        $this->appConfig = $appConfig;
        $this->connMapper = new ConnectionMapper();
        $jsonLoader = new JSONLoader();
        $this->framer = new Framer();
        $this->builder = new Builder($logger);
        $this->logger = $logger;
        $blocks = array("action" => "Action", "auth" => "Auth", "analytics" => "Analytics");
        foreach ($blocks as $key => $block) {
            $this->blockConfig[$key] = $jsonLoader->loadJSONConfig(__DIR__."/config/ClientData/".$block."Block.json");
        }
        $this->routeConfig = $jsonLoader->loadJSONConfig(__DIR__."/config/RouterConfigs/Routes.json");
        $this->setConnection(new Connection());
    }

    /**
     * @param Agents\Core\Connection $container
     */
    public function setConnection($container)
    {
        $this->connection = $container;
    }

    public function getConnection()
    {
        return clone $this->connection;
    }

    /**
     * Attaches a connection in the UCR
     *
     * @param $conn Socket\Connection
     *
     * @return string
     */
    public function newConnection($conn)
    {
        $result = $this->connMapper->connect($conn);
        $newConNotice = 'Connection '.$result.' is now connected';
        $this->connEventNotify($newConNotice, 'conn');
        $this->logger->addInfo($newConNotice);
        return $result;
    }
    public function setConnRoute($connId, $route)
    {
        $this->connMapper->updateRoute($connId, $route);
        $this->connEventNotify('Connection '.$connId.' changed route to '.$route, 'route');
        return $this;
    }
    public function setConnUser($connId, $user)
    {
        $this->connMapper->setUser($connId, $user);
        $connSetNotice = 'Connection '.$connId.' authenticated as '.$user;
        $this->connEventNotify($connSetNotice, 'auth');
        $this->logger->addInfo($connSetNotice);
        return $this;
    }

    /**
     * Cleans up the UCR after a client disconnects
     *
     * @param $connId integer
     * @param $conn Socket\Connection|null
     * @return self
     */
    public function killConnection($connId, &$conn = null)
    {
        $this->connMapper->disconnect($connId);
        if (!is_null($conn)) {
            $conn->end();
        }

        $killConnNotice = 'Connection '.$connId.' has disconnected or been killed';
        $this->connEventNotify($killConnNotice, 'kill');
        $this->logger->addInfo($killConnNotice);

        return $this;
    }

    public function connInfo()
    {
        return $this->connMapper->getConnMap();
    }

    public function recentEventCount()
    {
        $currTime = time();

        $recent = [
            1 => 0,
            2 => 0,
            3 => 0,
            4 => 0,
            5 => 0
        ];

        foreach ($recent as $minute => $count) {
            for ($i = 0; $i <= 60; $i++) {
                if (isset($this->eventCount[$currTime])) {
                    $recent[$minute] += $this->eventCount[$currTime];
                }
                $currTime--;
            }
        }

        return $recent;
    }

    public function cleanEventCount($time)
    {
        foreach ($this->eventCount as $second => $count) {
            if ($second < $time) {
                unset($this->eventCount[$second]);
            }
        }
    }

    public function connEventInfo($trigger = '', $type = '')
    {
        $conns = $this->connInfo();

        $connInfo = [
            'conns' => $conns,
            'info' => [
                'message' => $trigger,
                'type' => $type
            ],
            'activity' => $this->recentEventCount()
        ];

        return $connInfo;
    }

    public function connEventNotify($trigger = '', $type = '')
    {
        $monitors = $this->connMapper->getConnMonitors();

        $connInfo = $this->connEventInfo($trigger, $type);

        foreach ($monitors as $monitor) {
            $monitor->write($this->framer->encode(json_encode($connInfo, JSON_FORCE_OBJECT)));
        }
    }

    /**
     * This method takes data from a client and passes it to the Interpreter
     *
     * @param $json string
     * @param $connId string|int
     * @return boolean
     *
     * @throws
     */
    public function handle($json, $connId, $requestId)
    {

        $eventTime = time();
        if (!isset($this->eventCount[$eventTime])) {
            $this->eventCount[$eventTime] = 0;
        }
        $this->eventCount[$eventTime] += 1;

        // decode the JSON message from the client
        // if malformed JSON or if the message is nested too deep, throw exception
        $data = json_decode($json, true);
        if(!($data)) {
            throw new EDException("GATEWAY::JSON cannot be decoded", 14);
        }

        // Check if auth is the only block that was provided
        $authOnly = false;
        if (array_key_exists('auth', $data)) {
            if (array_key_exists('action', $data) && !isset($data['action']['route']) || count($data) == 1) {
                $authOnly = true;
            }
        }

        if (!$authOnly) {
            if (isset($data['mode'], $data['key']) && $data['mode'] === 'connection' && $data['key'] === '3ard1sh') {
                $this->connMapper->setConnMonitor($connId);
                $connInfo = $this->connEventInfo('You now have a working monitoring session', 'init');
                $this->logger->addInfo('You have a working monitoring session');
                $connsEncoded = $this->framer->encode(json_encode($connInfo, JSON_FORCE_OBJECT));
                $this->connMapper->getConnObj($connId)->write($connsEncoded);
                return true;
            }

            if (isset($data['mode']) && $data['mode'] === 'refresh_monitor' && $this->connMapper->isConnMonitor($connId)) {
                $connInfo = $this->connEventInfo('This is the update you requested', 'update');
                $connsEncoded = $this->framer->encode(json_encode($connInfo, JSON_FORCE_OBJECT));
                $this->connMapper->getConnObj($connId)->write($connsEncoded);
                return true;
            }
        }

        /**
         * @var Interpreter
         */
        $interpreter = new Interpreter($data, $connId, $this->blockConfig, $this->routeConfig, $this->logger, $authOnly);

        // return early if nothing of value was given
        if (!isset($data['action']) && !isset($data['auth']) && !isset($data['analytics'])) {
            $this->logger->addWarning('No action was given');

            return false;
        }

        // Set up DTO and connection Object
        /**
         * @var \Eardish\DataObjects\Request
         */
        $dto = $interpreter->getDataObjects();
        $dto->injectBlock(new AuditBlock());
        $dto->injectBlock(new MetaBlock());


        $conn = $this->connMapper->getConnObj($connId);

        if (isset($data['action']['responseToken'])) {
            $dto->getActionBlock()->setResponseToken($data['action']['responseToken']);
        }
        // Auth user if they are authable and they currently dont have a profileId assigned to their connection
        if ($dto->getAuthBlock()) {
            if ($dto->getAuthBlock()->isAuthable()) {
                if (!$interpreter->validateBlocks(array('auth'))) {
                    throw new EDInvalidOrMissingParameterException("GATEWAY::unable to validate data");
                }
                // admin user for AR tools
                if ($dto->getAuthBlock()->getEmail() == 'artoolsadmin@eardish.com' && $dto->getAuthBlock()->getPassword() == 'pa$$4arToolsAdmin') {
                    $dto->getMetaBlock()->setCurrentProfile(0);
                    $conn->setConnAuth();
                    $this->sendToBridge($authOnly, $interpreter, $conn, $connId, $dto, $eventTime, $data);
                } else {
                    $this->initRequest($requestId, $dto);
                    $this->handleIsAuthable($dto, $conn, $connId, $authOnly, $requestId, $interpreter, $eventTime, $data);
                }
            } else {
                $this->logger->addInfo('User was not able to be authenticated');
                throw new EDInvalidOrMissingParameterException("GATEWAY::the request does not contain the correct parameters to login");

            }
        } else {
            $dto->getMetaBlock()->setCurrentProfile($conn->getProfileId());
            $this->sendToBridge($authOnly, $interpreter, $conn, $connId, $dto, $eventTime, $data);
        }
    }

    /**
     * @param $authOnly
     * @param $interpreter Interpreter
     * @param $conn /Agents/Core/Connection
     * @param $connId
     * @param $dto Request
     * @param $eventTime
     * @return bool
     * @throws EDException
     * @throws EDInvalidOrMissingParameterException
     */
    function sendToBridge($authOnly, $interpreter, $conn, $connId, $dto, $eventTime, $data) {
        // return early if no action block present
        if (!isset($data['action']) || $authOnly) {
            $this->logger->addWarning('No action block present');
            //$this->builder->buildResponder($data);
            return true;
        }

        // Validate the analytics block if its set
        if (isset($data['analytics']) && !$interpreter->validateBlocks(array('analytics'))) {
            throw new EDInvalidOrMissingParameterException("GATEWAY::unable to validate analytics block");
        }
        // return early if the action block doesnt validate and it wasnt an analytics request. Analytics request dont need action validated (yet)
        if (!isset($data['analytics']) && !$interpreter->validateBlocks(array('action'))) {
            throw new EDInvalidOrMissingParameterException("GATEWAY::unable to validate actionBlock");
        }

        // Set route flag for UCR connections
        $conn->setConnRoute();
        // userId will be false if user is not authed
        $this->setConnUser($connId, $this->connMapper->getUserByConn($connId));
        $dto->getMetaBlock()->setCurrentUser($this->connMapper->getUserByConn($connId));

        // add connId to the DTO
        $dto->getMetaBlock()->setConnId($connId);

        $route = $dto->getActionBlock()->getRoute();
        // throw exception -- send back a message saying the request was wrong
        if (!$route) {
            throw new EDException("GATEWAY::invalid route", 13);
        }

        $this->setConnRoute($connId, $route);

        $dto = $interpreter->getRouter()->addRouting($route, $dto, $conn);
        if(!$interpreter->validateRouteData($interpreter->getRouter()->getRouteData(), $conn)) {
            throw new EDInvalidOrMissingParameterException("GATEWAY::invalid client data for given route");
        }
        // exceptional conditions caught inside of BridgeAgent and Connection
        $dto->getMetaBlock()->setFqdn($this->host);

        $slc = new BridgeAgent($this->getConnection(), $this->appConfig);
        $slc->sendToSLC($dto);

        unset($slc, $interpreter);

        $this->cleanEventCount( ($eventTime - 300) );

        return true;
    }

    public function handleIsAuthable(Request $dto, Socket\Connection $conn, $connId, $authOnly, $requestId, $interpreter, $eventTime, $data)
    {
        $this->setRequests([
            function() use ($requestId, $dto) {
                $auth = new AuthAgent($this->getConnection(), $this->appConfig);
                $auth->authenticate($dto->getAuthBlock()->getEmail(), $dto->getAuthBlock()->getPassword(), $requestId);
            },
            function($response, $previousIndex) use ($connId, $conn, $authOnly, $dto, $requestId) {
                $result = [];
                $profileId = false;
                $userId = false;
                $onboarded = false;
                $profileType = "fan";

                // set onboarded to false if it is null (may come back null from the database)
                if ($response['data'][$previousIndex]) {
                    $result = $response['data'][$previousIndex];
                    $profileId = $result['profileId'];
                    $userId = $result['userId'];
                    $profileType = $result['profileType'];
                    $onboarded = $result['onboarded'];
                    if (!$response['data'][$previousIndex]['onboarded']) {
                        $result['onboarded'] = false;
                    } else {
                        $result['onboarded'] = true;
                    }
                }

                if (count($result) && $profileId && $userId) {
                    $conn->setProfileId($profileId);
                    $this->setConnUser($connId, $userId);
                    $dto->getMetaBlock()->setCurrentUser($userId);
                    $dto->getMetaBlock()->setCurrentProfile($profileId);
                    $dto->getMetaBlock()->setProfileType($profileType);
                    //set ConnAuth flag to true
                    $conn->setConnAuth();
                    $this->logger->addInfo($dto->getAuthBlock()->getEmail()." logged in successfully",
                        [
                            'conn-id' => $connId
                        ]);

                    $message = array(
                        "from" => "system",
                        "type" => "toast",
                        "content" => $dto->getAuthBlock()->getEmail()." logged in successfully",
                        "destination" => "none",
                    );

                    $statusCode = 1;

                    $data = array(
                        "profileId" => $profileId,
                        "userId" => $userId,
                        "onboarded" => $onboarded,
                    );

                    // If there is a responseToken, be sure to return it.
                    if (($dto->blockExists("action")) && $dto->getActionBlock()->getResponseToken()) {
                        $meta = array("responseToken" => $dto->getActionBlock()->getResponseToken());
                    } else {
                        $meta = null;
                    }

                    $notification = $this->builder->prepareMessageResponder(
                        $message,
                        $statusCode,
                        $meta,
                        $data
                    );
                } else {
                    $this->logger->addInfo($dto->getAuthBlock()->getEmail()." could not be logged in",
                        [
                            'conn-id' => $connId
                        ]);
                    $message = array(
                        "from" => "system",
                        "type" => "toast",
                        "content" => $dto->getAuthBlock()->getEmail()." could not be logged in",
                        "destination" => "none"
                    );
                    $statusCode = 11;
                    $meta = null;
                    // If there is a responseToken, be sure to return it.
                    if (($dto->blockExists("action")) && $dto->getActionBlock()->getResponseToken()) {
                        $meta = array(
                            "responseToken" => $dto->getActionBlock()->getResponseToken()
                        );
                    }

                    $notification = $this->builder->prepareMessageResponder(
                        $message,
                        $statusCode,
                        $meta
                    );
                }
                if (!$conn->isConnAuthed() || $authOnly == true) {
                    $conn->write($notification);
                } else {
                    $this->connMapper->setConnResponseTokenData($connId, $dto->getMetaBlock()->getResponseToken(), $notification);
                }
                $this->next(['requestId' => $requestId], true);
            },
            function() use ($authOnly, $interpreter, $conn, $connId, $dto, $eventTime, $data) {
                return $this->sendToBridge($authOnly,$interpreter, $conn, $connId, $dto, $eventTime, $data);
            }
        ], $requestId);

        $this->first($requestId);
    }

    /**
     * This method takes in data from the SLC and sends it to the Builder
     *
     * @param $data Response
     * @param $bridgeConn /Eardish/Gateway/Socket/Connection
     */
    public function build($data, $bridgeConn)
    {
        $unserializeResponse = "response failed on unserialize";

        if (strlen($data) >= $bridgeConn->bufferSize) {
            throw new EDTransportException("GATEWAY:: transport exception:: $unserializeResponse");
        }

        /**
         * @var $responseObject \Eardish\DataObjects\Response
         */
        $responseObject = unserialize(utf8_decode($data));
        if (!($responseObject instanceof Response)) {
            throw new EDTransportException("GATEWAY:: transport exception:: $unserializeResponse");
        }
        // CODE THAT GETS THE FIRST PART OF THE REQUEST RESPONSE. NOT USING THIS FOR NOW. MOSTLY JUST AUTH RESPONDERS
        //$savedResponse = $this->connMapper->getConnResponseTokenData($responseObject->getMetaBlock()->getConnId(), $responseObject->getMetaBlock()->getResponseToken()));

        $responder = $this->builder->buildResponder($responseObject);

        /**
         * @var
         */
        $conn = $this->connMapper->getConnObj($responseObject->getMetaBlock()->getConnId());
        if (!(is_object($conn)) || !($conn->write($responder))) {
            throw new EDConnectionWriteException("Failure to write back to client. Connection has been terminated");
        }
        unset($responder, $responseObject);
    }

    /**
     * @param $data
     * @return string
     */
    public function buildException(AuditBlock $data)
    {
        return $this->builder->handleException($data);
    }

    public function getBuilder()
    {
        return $this->builder;
    }

    public function getConnectionMapper()
    {
        return $this->connMapper;
    }

    public function getAppConfig()
    {
        return $this->appConfig;
    }
}