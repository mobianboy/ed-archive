<?php
namespace Eardish\Cron\CronProcessors;

use Eardish\Cron\CronProcessors\Core\AbstractProcess;

class ProcessDeletedItems extends AbstractProcess
{
    protected $tempDeletedItems;
    protected $deletedQueue;

    public function __construct()
    {
        parent::__construct();
    }

    public function processDeletedJobs($entry)
    {
        $table = $entry['label'];
        $itemToDelete = json_decode($entry['data'], true);
        $relationshipBool = $this->cronConfig['tables'][$table]['isRelationship'];

        if ($relationshipBool) {
            $this->removeRelationship($table, $itemToDelete);
        } else {
            $this->removeNodeAndRelationships($table, $itemToDelete);
        }
        $this->deleteFromElastic($table, $itemToDelete);
    }

    protected function removeNodeAndRelationships($table, $itemToDelete)
    {
        // find node and delete all its relationships, then delete node
        $node = $this->getNeoNode($table, 'id', $itemToDelete['id']);
        $nodeRelationships = $this->neoClient->getNodeRelationships($node);
        $nodeRelationships->delete();
        $node->delete();

        //if node is user or group, also delete its profile node
        if ($table == 'user') {
            $profileNode = $this->getNeoNode('user_profile', 'user_id', $itemToDelete['id']);
            $profileNode->delete();
        }
        if ($table == 'group') {
            $profileNode = $this->getNeoNode('group_profile', 'group_id', $itemToDelete['id']);
            $profileNode->delete();
        }
    }

    protected function removeRelationship($table, $itemToDelete)
    {
        // find nodes connecting the relationship using the config and delete the relationship
        $relationshipName = $this->cronConfig['tables'][$table]['relationshipName'];
        $relationship = $this->getNeoRelationById($relationshipName, 'id', $itemToDelete['id']);
        $relationship->delete();
    }

    protected function deleteFromElastic($table, $itemToDelete)
    {
        $searchParams = array();
        $searchParams['index'] = 'eardish';
        $searchParams['type'] = $table;
        $searchParams['body']['query']['match']['id'] = $itemToDelete['id'];
        $response = $this->elasticClient->delete($searchParams);

        //a status code of 201 means the document was successfully deleted
        if ($response['status'] != '201') {
            //TODO write to logger with $response['status']
        }
    }
}
