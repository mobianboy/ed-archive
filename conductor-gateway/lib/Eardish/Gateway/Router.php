<?php
namespace Eardish\Gateway;

use Eardish\DataObjects\Blocks\RouteBlock;
use Eardish\DataObjects\Request;
use Eardish\Gateway\Socket\Connection;
use Eardish\Gateway\config\ArrayFlattener;
use \Eardish\Exceptions\EDException;
use Eardish\Exceptions\EDInvalidOrMissingParameterException;

class Router
{
    /**
     * @var array
     */
    protected $routeConfig;

    protected $flatRoutes;

    protected $routeParts;

    public function __construct(array $routeConfig)
    {
        $this->routeConfig = $routeConfig;
    }

    public function getRouteData()
    {
        $data = $this->routeConfig;
        foreach ($this->routeParts as $part) {
            $data = $data[$part];
        }

        return $data["required"];
    }

    /**
     * @param $route
     * @param Request $dto
     * @param Connection $conn
     * @return Request
     */
    public function addRouting($route, Request $dto, Connection $conn)
    {
        $arrayFlattener = new ArrayFlattener();
        $this->flatRoutes = $arrayFlattener->recurseArray($this->routeConfig);
        $this->routeParts = $parts = explode("/", $route);
        $action = array_pop($parts);

        if (!$conn->isConnAuthed() && ($action == "update" || $action == "delete")) {
            $action = "get";
        }

        array_push($parts, $action);
        $controller = $parts[0];
        $route = implode(".", $parts);

        if (array_key_exists($route.".method", $this->flatRoutes)) {
            $routeBlock = new RouteBlock($this->flatRoutes[$controller.".controller"], $this->flatRoutes[$route.".method"]);
            $dto->injectBlock($routeBlock);
        } else {
            throw new EDInvalidOrMissingParameterException("GATEWAY::invalid client data for given route");
        }

        return $dto;
    }
}
