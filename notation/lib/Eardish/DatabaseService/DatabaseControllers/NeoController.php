<?php
namespace Eardish\DatabaseService\DatabaseControllers;

use Everyman\Neo4j\Client;
use Everyman\Neo4j\Cypher\Query;
use Everyman\Neo4j\Query\ResultSet;
use Everyman\Neo4j\PropertyContainer;

class NeoController
{
    /**
     * @var Client
     */
    protected $neo4j;

    public function __construct()
    {
        $this->neo4j = new Client();
    }

    /**
     * Get node from neo4j by defining the label and it's name
     *
     * @param $query string
     * @return array
     */
    public function select($query)
    {
        $results = array();

        $query = new Query($this->neo4j, $query);

        $resultSet = $query->getResultSet();

        foreach ($resultSet as $row) {
            if (is_object($row['raw'])) {
                $results[] = $row['raw']->getProperties();
            } else {
                $results[] = $row['raw'];
            }
        }
        return $results;
    }

    /**
     * @return Client
     */
    public function getNeo4j()
    {
        return $this->neo4j;
    }
}
