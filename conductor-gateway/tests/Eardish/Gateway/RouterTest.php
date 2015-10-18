<?php
namespace Eardish\Gateway;

use Eardish\DataObjects\Request;
use Eardish\DataObjects\Blocks\ActionBlock;
use Eardish\Gateway\config\JSONLoader;
use Eardish\Gateway\config\ArrayFlattener;

class RouterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Router
     */
    protected $router;
    protected $mockRoute;

    public function setUp()
    {
        $jsonLoader = new JSONLoader();
        $this->router = new Router($jsonLoader->loadJSONConfig(__DIR__."/../../../lib/Eardish/Gateway/config/RouterConfigs/Routes.json"));
    }

    public function testAddRouting()
    {
        $route = "user/create";

        $conn = $this->getMockBuilder('Eardish\\Gateway\\Socket\\Connection')
            ->setMethods(array("end"))
            ->disableOriginalConstructor()
            ->getMock();

        $conn->setResourceId(10);

        $conn->setConnAuth();

        $params = [
            new ActionBlock($route, 10)
        ];

        $dto = new Request($params);

        return $dto;
    }

    public function testAddRoute()
    {
        $route = "profile/genre/blend/update";

        $conn = $this->getMockBuilder('Eardish\\Gateway\\Socket\\Connection')
            ->setMethods(array("end"))
            ->disableOriginalConstructor()
            ->getMock();

        $conn->setResourceId(10);


        $params = [
            new ActionBlock($route, 10)
        ];

        $dto = new Request($params);

        $this->assertEquals(
            "getProfileGenreBlend",
            $this->router->addRouting($route, $dto, $conn)->getRouteBlock()->getControllerMethod()
        );

        $conn->setConnAuth();

        $this->assertEquals(
            "modifyProfileGenreBlend",
            $this->router->addRouting($route, $dto, $conn)->getRouteBlock()->getControllerMethod()
        );

        $this->assertEquals(
            "Profile",
            $this->router->addRouting($route, $dto, $conn)->getRouteBlock()->getControllerName()
        );
    }

    public function testGetRouteData()
    {
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

        $this->router->addRouting($route, $dto, $conn);

        $this->assertEquals(
            [
                'data' =>
                [
                    'id' => "int,,positive",
                    'genres-liked' => array("int,,positive"),
                    'genres-disliked' =>  array("int,,positive")
                ],
                'auth' => []
            ],
            $this->router->getRouteData()
        );
    }
}
