<?php
namespace Eardish\Cron\CronProcessors;

use Eardish\Cron\CronProcessors\Core\AbstractProcess;
use Everyman\Neo4j\Node;

class ProcessNewItems extends AbstractProcess
{
    public function __construct()
    {
        parent::__construct();
    }

    public function processNewJobs($entry)
    {
        $table = $entry['label'];
        $itemToCreate = json_decode($entry['data'], true);
        $relationshipBool = $this->cronConfig['tables'][$table]['isRelationship'];

        if ($relationshipBool) {
            $this->createRelationship($table, $itemToCreate);
        } else {
            $this->createNode($table, $itemToCreate);
        }
        $this->addToElastic($table, $itemToCreate);
   }

    private function createNode($table, $itemToCreate)
    {
        //Create Neo Node
       $this->insertNeo($table, $itemToCreate);
    }

    protected function createRelationship($table, $itemToCreate)
    {
        $relationshipName = $this->cronConfig['tables'][$table]['relationshipName'];

        $nodeConfigs = $this->cronConfig['tables'][$table]['nodes']; //will be an array of the label names connecting the relationship

        $nodes = array();
        foreach ($nodeConfigs as $label => $lookupField) {
            $nodes[] = $this->getNeoNode($label, 'id', $itemToCreate[$lookupField]);
        }

        /**
         * @var $node1 Node
         * @var $node2 Node
         */
        list($node1, $node2) = $nodes;
        //TODO merge nodes/ relationships if they already exist to avoid duplication
        $this->makeNeoRelation($node1, $node2, $relationshipName, $itemToCreate);
    }

    protected function addToElastic($table, $itemToCreate)
    {
        $params = array();
        $params['index'] = 'eardish';
        $params['type'] = $table;
        $params['body'] = $itemToCreate;
        $response = $this->elasticClient->create($params);
        if (!$response['created']) {
            //TODO log error if not created
        }
        //a status code of 201 means the document was successfully created
//
    }

}