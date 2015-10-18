<?php
namespace Eardish\Cron\CronProcessors\Core;

use Everyman\Neo4j\Client;
use Elasticsearch\Client as ElasticClient;
use Everyman\Neo4j\Cypher\Query;
use Everyman\Neo4j\Node;
use Everyman\Neo4j\Relationship;

abstract class AbstractProcess
{
    protected $jsonLoader;
    protected $cronConfig;
    protected $neoClient;
    protected $elasticClient;


    public function __construct()
    {
        $this->jsonLoader = new JSONLoader();

        $this->cronConfig = $this->jsonLoader->loadJSONConfig(__DIR__ . "/../../CronConfig/CronConfig.json");
        $this->neoClient = new Client();
        $this->elasticClient = new ElasticClient();
    }

    /**
     * Insert into neo4j (this code will not stay here, may be used elsewhere)
     *
     * @param $label
     * @param array $properties
     * @return string
     * @throws \Everyman\Neo4j\Exception
     */
    public function insertNeo($label, array $properties = array())
    {
        if (!is_array($properties) || empty($properties)) {
            return 'Properties must be a non-empty array.';
        }
        $label = $this->neoClient->makeLabel($label);
        $node = $this->neoClient->makeNode($properties);
        $node->save();
        $node->addLabels(array($label));

    }

    /**
     * Used to create a relationship between two nodes
     *
     * @param $node Node
     * @param $otherNode Node
     * @param $relation string
     * @param $properties array
     */
    public function makeNeoRelation(Node $node, Node $otherNode, $relation, array $properties = array())
    {
        $node->relateTo($otherNode, $relation)->setProperties($properties)->save();
    }


    /**
     * retrieve relation by id in properties
     *
     * @param $properties array
     * @param $relation
     * @return Relationship
     */
    public function getNeoRelationById($relation, $properties)
    {
        $id = $properties['id'];
        $queryString = "MATCH ()-[r:$relation {id: $id}]-() RETURN r";
        $query = new Query($this->neoClient, $queryString);
        $results = $query->getResultSet();
        foreach ($results as $row) {
            return $row['raw'];
        }
    }


    /**
     * Returns the node matching the given column and value
     *
     * @param $label
     * @param $column
     * @param $value
     * @return Node
     */
    public function getNeoNode($label, $column, $value)
    {
        $queryString = "MATCH (n:$label) WHERE n.$column = '$value' RETURN n";


        $query = new Query($this->neoClient, $queryString);
        $results = $query->getResultSet();
        foreach ($results as $row) {
            return $row['raw'];
        }
    }
}
