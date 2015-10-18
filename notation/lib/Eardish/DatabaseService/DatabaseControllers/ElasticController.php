<?php
namespace Eardish\DatabaseService\DatabaseControllers;

use Eardish\Cron\CronQueues\DeletedItems;
use Eardish\Cron\CronQueues\ModifiedItems;
use Eardish\Cron\CronQueues\NewItems;
use Elasticsearch\Client;
use Eardish\DatabaseService\DatabaseControllers\PostgresController;

/**
  * A lot of reused code here, can consolidate to reduce reuse. Will look into that later.
 *
 * Class ElasticController
 * @package Eardish\DatabaseService\DatabaseControllers
 */
class ElasticController
{
    /**
     * @var Client
     */
    protected $client;
    protected $index;

    public function __construct()
    {
        $this->client = new Client();
        $this->index = 'eardish'; //the index is like the database (the root)
    }

    /**
     * Inserts a 'document' into ES at the given index and type. Might be pulled
     * out and used elsewhere.
     *
     * @param $type
     * @param string $id
     * @param array $body
     * @return array
     */
    public function insert($type, $id = '', array $body)
    {
        if (empty($id)) {
            $arr = array('index'=>$this->index, 'type'=>$type, 'body'=>$body);
        } else {
            $arr = array('index'=>$this->index, 'type'=>$type, 'id'=>$id, 'body'=>$body);
        }

        return $this->client->index($arr);
    }

    public function update($type, $id = '', array $body)
    {
        if (empty($id)) {
            $arr = array('index'=>$this->index, 'type'=>$type, 'body'=>$body);
        } else {
            $arr = array('index'=>$this->index, 'type'=>$type, 'id'=>$id, 'body'=>array('doc'=>$body));
        }

        return $this->client->update($arr);
    }

    public function get($type, $id)
    {
        $arr = array('index'=>$this->index, 'type'=>$type, 'id'=>$id);

        return $this->client->get($arr);
    }

    // Could update to optionally return only top result
    public function select($queryArray, $type)
    {
        $query = array('index'=>$this->index, 'type'=>$type);
        $query['body'] = $queryArray;
        $queryResponse = $this->client->search($query);
        return $queryResponse['hits']['hits']['0'];
    }

    public function getClient()
    {
        return $this->client;
    }
}
