<?php
namespace Eardish\Gateway;

use Eardish\DataObjects\Blocks\ActionBlock;
use Eardish\DataObjects\Request;
use Eardish\Gateway\config\JSONLoader;

class InterpreterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Interpreter
     */
    protected $interpreter;
    protected $blockConfig;
    protected $routes;

    public function setUp()
    {
        //changes here affect json blocks in ClientData, RequestObjects in DataObjects repo, InterpreterTest,
        // and Request and RequestTest in DataObjects repo
        $blocks = array("action" => "Action", "auth" => "Auth", "analytic" => "Analytic");
        $jsonLoader = new JSONLoader();
        $this->routes = $jsonLoader->loadJSONConfig(__DIR__."/../../../lib/Eardish/Gateway/config/RouterConfigs/Routes.json");
        foreach ($blocks as $key => $block) {
            $this->blockConfig[$key] = $jsonLoader->loadJSONConfig(__DIR__."/../../../lib/Eardish/Gateway/config/ClientData/".$block."Block.json");
        }

        $str = '{
                    "action": {
                        "route": "/echo/echoText/data",
                        "priority": 1
                    },
                    "data": {
                        "id": 1,
                        "name": "Some Name",
                        "email": "someone@gmail.com",
                        "date": "1/4/1991",
                        "bio": "We are awesome",
                        "website": "www.facebook.com"
                    },
                    "audio": {
                        "artistName": "Linkin Park",
                        "songTitle": "In the End",
                        "copyrightNotice": "copyright 2001",
                        "creationDate": "1/4/2001",
                        "sizeOfFile": 3445,
                        "ownership": true,
                        "encodingType": "mp3"

                    },
                    "auth": {
                        "email": "email@eardish.com",
                        "password": "samplePass"
                    }
                }';

        $this->interpreter = new Interpreter(json_decode($str, true), 12345, $this->blockConfig, $this->routes);
    }

    public function testGetDataObjects()
    {
        $str = '{
                    "action": {
                        "route": "/echo/echoText/data",
                        "priority": 1
                    },
                    "auth": {
                        "email": "email@eardish.com",
                        "password": "samplePass"
                    }
                }';

        $interpreter2 = new Interpreter(json_decode($str, true), 12345, $this->blockConfig, $this->routes);

        $dto = $interpreter2->getDataObjects();

        $response = $this->interpreter->getDataObjects();

        $this->assertInstanceOf(
            "Eardish\\DataObjects\\Request",
            $response
        );

        $this->assertEquals(
            '1',
            $response->getActionBlock()->getPriority()
        );

        $this->assertInstanceOf(
            'Eardish\\DataObjects\\Request',
            $dto
        );
    }
    public function testGetVal()
    {
        // test getting specific value in action's contents
        $this->assertEquals(
            '/echo/echoText/data',
            $this->interpreter->getVal('action.route', false)
        );

        $this->assertEquals(
            '1',
            $this->interpreter->getVal('action.priority', false)
        );
    }

    public function testGetRouter()
    {
        $this->assertEquals(
            $this->interpreter->getRouter(),
            new Router($this->routes)
        );
    }

    public function testValidateGivenData()
    {
        //priority introduces error to make assertion correct
        $str = '{
                    "action": {
                        "route": "/echo/echoText/data",
                        "priority": 1,
                        "responseToken": 478246201
                    },
                    "data": {
                        "id": 1,
                        "name": "Some Name",
                        "email": "someone@gmail.com",
                        "date": "1/4/1991",
                        "bio": "We are awesome",
                        "website": "www.facebook.com"
                    },
                    "audio": {
                        "artistName": "Linkin Park",
                        "songTitle": "In the End",
                        "copyrightNotice": "copyright 2001",
                        "creationDate": "1/4/2001",
                        "sizeOfFile": 3445,
                        "ownership": true,
                        "encodingType": "mp3"

                    },
                    "auth": {
                        "email": "email@eardish.com",
                        "password": "samplePass"
                    }
                }';

        $this->interpreter = new Interpreter(json_decode($str, true), 12345, $this->blockConfig, $this->routes);


        $this->assertTrue(
            $this->interpreter->validateBlocks(array('action'))
        );

        $str = '{
                    "action": {
                        "route": "/artist/1922",
                        "priority": 10
                    },
                    "data": {
                        "id": 1,
                        "name": "Some Name",
                        "email": "someone@gmail.com",
                        "date": "1/4/1991",
                        "bio": "We are awesome",
                        "website": "www.facebook.com"
                    },
                    "audio": {
                        "artistName": "Linkin Park",
                        "songTitle": "In the End",
                        "copyrightNotice": "copyright 2001",
                        "creationDate": "1/4/2001",
                        "sizeOfFile": 3445,
                        "ownership": true,
                        "encodingType": "mp3"

                    },
                    "auth": {
                        "email": "email@eardish.com",
                        "password": "samplePass"
                    }
                }';

        $this->interpreter = new Interpreter(json_decode($str, true), 12345, $this->blockConfig, $this->routes);

        $this->assertTrue(
            $this->interpreter->validateBlocks(array('auth'))
        );

        $data = '{
                    "action": {
                        "route": "/artist/1284",
                        "priority": 10
                    },
                    "auth": {
                        "email": "email@eardish.com",
                        "password": "samplePass"
                    }
                }';

        $this->interpreter = new Interpreter(json_decode($data, true), 12345, $this->blockConfig, $this->routes);
        $this->assertTrue(
            $this->interpreter->validateBlocks(array('auth')));
    }

    public function testValidateRouteData()
    {
        $str = '{
                    "action": {
                        "route": "profile/genre/blend/create",
                        "priority": 10
                    },
                    "data": {
                        "id": 1,
                        "genres-liked": [
                            1,
                            2,
                            3
                        ],
                        "genres-disliked": [
                            4
                        ]
                    },
                    "auth": {}
                }';


        $interpreter = new Interpreter(json_decode($str, true), 12345, $this->blockConfig, $this->routes);

        $route = "profile/genre/blend/create";

        $conn = $this->getMockBuilder('Eardish\\Gateway\\Socket\\Connection')
            ->setMethods(array("end"))
            ->disableOriginalConstructor()
            ->getMock();

        $conn->setResourceId(10);


        $params = [
            new ActionBlock($route, 10)
        ];


        $dto = new Request($params);

        $conn->setConnAuth();

        $interpreter->getRouter()->addRouting($route, $dto, $conn);

        $data = $interpreter->getRouter()->getRouteData();

        $this->assertTrue(
            $interpreter->validateRouteData($data, $conn)
        );
    }

    public function testValidateRouteData2()
    {
        $str = '{
                    "action": {
                        "route": "user/create",
                        "priority": 10
                    },
                    "data": {
                        "id": 1,
                        "password": "password",
                        "passwordConfirmation": "password",
                        "email": "test@eardish.com",
                        "name": {
                            "first" : "ear",
                            "last": "dish"
                        },
                        "type": "user",
                        "yearOfBirth": 1967,
                        "zipcode": "91401"
                    },
                    "auth": {}
                }';


        $interpreter = new Interpreter(json_decode($str, true), 12345, $this->blockConfig, $this->routes);

        $route = "user/create";

        $conn = $this->getMockBuilder('Eardish\\Gateway\\Socket\\Connection')
            ->setMethods(array("end"))
            ->disableOriginalConstructor()
            ->getMock();

        $conn->setResourceId(10);


        $params = [
            new ActionBlock($route, 10)
        ];


        $dto = new Request($params);

        $conn->setConnAuth();

        $interpreter->getRouter()->addRouting($route, $dto, $conn);

        $data = $interpreter->getRouter()->getRouteData();

        $this->assertTrue(
            $interpreter->validateRouteData($data, $conn)
        );
    }
}
