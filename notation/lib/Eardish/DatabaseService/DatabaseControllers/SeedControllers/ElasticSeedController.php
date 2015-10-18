<?php
namespace Eardish\DatabaseService\DatabaseControllers\SeedControllers;

use Eardish\DatabaseService\DatabaseControllers\ElasticController;

class ElasticSeedController extends ElasticController {

    public function __construct()
    {
        parent::__construct();
        $this->database = new PostgresSeedController();

    }

    public function insertSeed($type, array $data)
    {
        $params = array();
        $params['body'] = $data;
        $params['index'] = $this->index;
        $params['type'] = strtolower($type);
        $params['id'] = $data['id'];
        $this->client->index($params);
    }
}