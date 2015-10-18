<?php
namespace Eardish\DatabaseService\DatabaseControllers\SeedControllers;

use Eardish\DatabaseService\DatabaseControllers\NeoController;
use Everyman\Neo4j\Cypher\Query;
use Everyman\Neo4j\Node;
use Everyman\Neo4j\Relationship;

class NeoSeedController extends NeoController
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Insert into neo4j (this code will not stay here, may be used elsewhere)
     *
     * @param $label
     * @param array $properties
     * @return string
     * @throws \Everyman\Neo4j\Exception
     */
    public function insertSeed($label, array $properties = array())
    {
        if (!is_array($properties) || empty($properties)) {
            return 'Properties must be a non-empty array.';
        }

        $label = $this->neo4j->makeLabel($label);
        $node = $this->neo4j->makeNode($properties);
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
    public function makeRelation(Node $node, Node $otherNode, $relation, array $properties = array())
    {
        $node->relateTo($otherNode, $relation)->setProperties($properties)->save();
    }


    /**
     * Used to find a relationship between two nodes
     *
     * @param $node
     * @param $otherNode
     * @param $relation
     * @return Relationship
     */
    public function getRelation($relation, $properties)
    {
      //  $relationship = $node->getRelationships($relation, $otherNode);/        $queryString = "MATCH (a:{$node})->[r:$relation]-(b:{$otherNode}) RETURN r";
//

        $queryString = "MATCH ()-[r:$relation {}]-() RETURN r";
        $query = new Query($this->neo4j, $queryString);
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
    public function getNode($label, $column, $value)
    {
        $queryString = "MATCH (n:$label) WHERE n.$column = '$value' RETURN n";

        $query = new Query($this->neo4j, $queryString);
        $results = $query->getResultSet();

        foreach ($results as $row) {
            return $row['raw'];
        }
    }
}