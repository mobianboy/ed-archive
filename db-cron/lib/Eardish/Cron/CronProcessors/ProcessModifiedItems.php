<?php
namespace Eardish\Cron\CronProcessors;

use Eardish\Cron\CronProcessors\Core\AbstractProcess;

class ProcessModifiedItems extends AbstractProcess
{
    protected $tempModifiedItems;
    protected $modifyQueue;

    public function __construct()
    {
        parent::__construct();
    }

    public function processModifiedJobs($entry)
    {
        $table = $entry['label'];
        $itemToModify = json_decode($entry['data'], true);
        $relationshipBool = $this->cronConfig['tables'][$table]['isRelationship'];

        if ($relationshipBool) {
            $this->updateRelationship($table, $itemToModify);
        } else {
            $this->updateNode($table, $itemToModify);
        }
        $this->modifyElastic($table, $itemToModify);
    }

    protected function updateNode($table, $itemToModify)
    {
        $node = $this->getNeoNode($table, 'id', $itemToModify['id']);
        $properties = $node->getProperties();

        foreach ($properties as $property) {
            $node->removeProperty($property);
        }
        $node->setProperties($itemToModify)->save();
    }

    protected function updateRelationship($table, $itemToModify)
    {
        $relationshipName = $this->cronConfig['tables'][$table]['relationshipName'];
        $relationship = $this->getNeoRelationById($relationshipName, 'id', $itemToModify['id']);
        $properties = $relationship->getProperties();

        foreach ($properties as $property) {
            $relationship->removeProperty($property);
        }
        $relationship->setProperties($itemToModify)->save();
    }

    protected function modifyElastic($table, $itemToModify)
    {
        $searchParams = array();
        $searchParams['index'] = 'eardish';
        $searchParams['type'] = $table;
        $searchParams['body']['query']['match']['id'] = $itemToModify['id'];
        $response = $this->elasticClient->update($searchParams);

        //a status code of 201 means the document was successfully updated
        if ($response['status'] != '201') {
            //TODO write to logger with $response['status']
        }
    }
}
