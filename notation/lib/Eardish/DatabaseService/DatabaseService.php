<?php
namespace Eardish\DatabaseService;

use Eardish\DatabaseService\Config\JSONLoader;
use Eardish\DatabaseService\DatabaseControllers\NeoController;
use Eardish\DatabaseService\DatabaseControllers\ElasticController;
use Eardish\DatabaseService\DatabaseControllers\PostgresController;

use \Eardish\Exceptions\EDException;
use \Eardish\Exceptions\EDInvalidOrMissingParameterException;


class DatabaseService
{
    /**
     * @var ElasticController
     */
    protected $elastic;

    /**
     * @var PostgresController
     */
    protected $postgres;

    /**
     * @var NeoController
     */
    protected $neo;

    protected $selectQueryConfig;
    protected $insertQueryConfig;
    protected $deleteQueryConfig;
    protected $updateQueryConfig;

    protected $queryBuilder;
    protected $queryConfig;
    protected $clientData;
    protected $whereBlock;
    protected $operation;
    protected $priority;
    protected $request;
    protected $service;
    protected $tables;
    protected $params;
    protected $query;

    protected $db;


    public function __construct($postgres, $neo, $elastic)
    {
        $this->postgres = $postgres;
        $this->neo = $neo;
        $this->elastic = $elastic;

        $jsonLoader = new JSONLoader();
        $this->selectQueryConfig = $jsonLoader->loadJSONConfig(__DIR__ . '/Config/SelectQuery.json');
        if(!($this->selectQueryConfig)) {
            throw new EDException("NOTATIONDBS: cannot load SelectQuery.json");
        }
        $this->insertQueryConfig = $jsonLoader->loadJSONConfig(__DIR__ . '/Config/InsertQuery.json');
        if(!($this->insertQueryConfig)) {
            throw new EDException("NOTATIONDBS: cannot load InsertQuery.json");
        }
        $this->deleteQueryConfig = $jsonLoader->loadJSONConfig(__DIR__ . '/Config/DeleteQuery.json');
        if(!($this->deleteQueryConfig)) {
            throw new EDException("NOTATIONDBS: cannot load DeleteQuery.json");
        }
        $this->updateQueryConfig = $jsonLoader->loadJSONConfig(__DIR__ . '/Config/UpdateQuery.json');
        if(!($this->updateQueryConfig)) {
            throw new EDException("NOTATIONDBS: cannot load UpdateQuery.json");
        }
    }

    public function buildQuery(array $clientData)
    {
        $this->clientData = $clientData;
        $this->priority = "priority";
        $this->operation = $clientData['operation'];

        //get the action/request we are looking for, ex: getPlaylists
        $this->request = $clientData['request'];
        $this->service = $clientData['service'];

        //select the correct json config to load the queries
        switch ($this->operation) {
            case "select":
                // find priority for select
                if (($this->clientData['priority'] == 10)) {
                     //priority of 10 means that real time data is needed and to call the block with the postgres query in it (the number will probably change)
                    $this->priority = "priority";
                }
                $this->queryConfig = $this->selectQueryConfig;
                break;
            case "insert":
                $this->queryConfig = $this->insertQueryConfig;
                $this->queryBuilder = $this->queryConfig[$this->service][$this->request];
                $model = $this->queryBuilder['model'];
                $columns = $this->queryBuilder['columns'];
                $clientData = $this->clientData;
                return $this->insertEntry($clientData, $model, $columns);

                break;
            case "delete":
                $this->queryConfig = $this->deleteQueryConfig;
                break;
            case "update":
                $this->priority = "default";
                $this->queryConfig = $this->updateQueryConfig;
                break;
            default:
                //TODO write to log
                throw new EDInvalidOrMissingParameterException("NOTATIONDBS: '$this->operation' is not a type of database action (one of SELECT/INSERT/UPDATE/DELETE)");
        }
        ////returns an array with the db as the key and the query as the value
        if(!(isset($this->queryConfig['request'][$this->service][$this->request]['queries'][$this->priority]))) {
            throw new EDInvalidOrMissingParameterException("NOTATIONDBS: data missing from query");
        }

        $this->queryBuilder = $this->queryConfig['request'][$this->service][$this->request]['queries'][$this->priority];
        //pull out the value

        if(!(isset($this->queryBuilder['db']))) {
            throw new EDInvalidOrMissingParameterException("NOTATIONDBS: data (db) missing from query");
        }

        $this->db = $this->queryBuilder['db'];

        //pull out the query
        if(!(isset($this->queryBuilder['query']))) {
            throw new EDInvalidOrMissingParameterException("NOTATIONDBS: missing query parameter");
        }
        $this->query = $this->queryBuilder['query'];

        //find tables and relevant data from client that will be need to build the query
        if(!(isset($this->queryConfig['request'][$this->service][$this->request]['tables']))) {
            throw new EDInvalidOrMissingParameterException("NOTATIONDBS: supporting data (db) missing from query");
        }
        $this->tables = $this->queryConfig['request'][$this->service][$this->request]['tables'];

        $this->fillParams();
        if (count($this->tables)) {
            $this->fillQueryVariables();
        }
        return $this->sendToDBCon($this->query);
    }

    public function stringReplace($variable, $data, $queryString, $sanitize = true)
    {
        if ($sanitize) {
            $data = pg_escape_string($data);
        }

        return str_replace('$'.$variable, $data, $queryString);
    }

    public function elasticStringReplace($variable, $data, $queryString)
    {
        $jsonQuery = json_encode($queryString);
        $jsonQuery = $this->stringReplace($variable, $data, $jsonQuery);

        return json_decode($jsonQuery, true);
    }

    public function fillQueryVariables()
    {
        foreach ($this->tables as $table => $columns) {
            $columnsToUpdate = array();
            $updateData = array();
            for ($i = 0; $i < count($columns); $i++) {
                $column = $columns[$i];
                if ($this->operation == 'update') {
                    if (isset($this->clientData['data'][$table][$column])) {
                        $updateData[] = $this->clientData['data'][$table][$column];
                        $columnsToUpdate[] = $column;
                    }
                    if ($i == count($columns)-1) {
                        $this->buildUpdateParams($table, $columnsToUpdate, $updateData);
                    }
                } else {
                    $queryVariable = $table . '_' . $column;
                    $queryData = $this->clientData['data'][$table][$column];
                    if ($this->db == "elastic") {
                        //if using elastic search, must convert the query to json string to search through all levels of the array to find variables
                        $this->query = $this->elasticStringReplace($queryVariable, $queryData, $this->query);
                    } else {
                        if (is_array($queryData)) {
                            $queryData = implode(", ", $queryData);
                        }
                        $this->query = $this->stringReplace($queryVariable, $queryData, $this->query);
                    }
                }
            }
        }
    }

    public function fillParams()
    {
        if ($this->hasParams()) {
            // find the param variables that will be evaluated in the query
            if (count($this->params) > 0 ) {
                foreach ($this->params as $key => $value) {
                    $paramData = $this->clientData['params'][$key];
                    if ($this->db == "elastic") {
                        //if using elastic search, must convert the query to json string to search through all levels of the array to find variables
                        $this->query = $this->elasticStringReplace($key, $paramData, $this->query);
                    }
                    $this->query = $this->stringReplace($key, $paramData, $this->query);
                }
            }
        }
    }

    public function hasParams()
    {
        if (array_key_exists("params", $this->queryConfig['request'][$this->service][$this->request])) {
            $this->params = $this->queryConfig['request'][$this->service][$this->request]['params'];

            return true;
        }
        return false;
    }

    public function buildUpdateParams($table, $columnsToUpdate, $updateData)
    {
        $from = array();
        $fromWhere = array();
        // find the where block parameters and replace with client data
        $this->whereBlock = $this->queryConfig['request'][$this->service][$this->request]['where'];
        $whereTables = array_keys($this->whereBlock);
        $whereTable = $whereTables[0];
        $whereColumns = $this->whereBlock[$whereTable];
        if (array_key_exists('from', $this->queryConfig['request'][$this->service][$this->request])) {
            $from = $this->queryConfig['request'][$this->service][$this->request]['from'];
        }
        if (array_key_exists('fromWhere', $this->queryConfig['request'][$this->service][$this->request])) {
            $fromWhere = $this->queryConfig['request'][$this->service][$this->request]['fromWhere'];
        }
        //send update to doctrine to build out unknown amount of parameters, then place them in out query string
        $updateQuery = $this->postgres->updateBuilder($table, $columnsToUpdate, $updateData, $whereTable, $whereColumns, $from, $fromWhere, $this->clientData);
        $this->query = $updateQuery;
    }

    // Send query string to DB Controller to be processed
    public function sendToDBCon($query = null)
    {
        if (!$query) {
            $query = $this->query;
        }
        if ($this->db == "elastic") {
            // sending the type with the query so we can use it to properly search
            $type = $this->queryConfig['request'][$this->service][$this->request]['type'];

            return $this->elastic->{$this->operation}($query, $type);
        } else if ($this->db == "neo") {
            return $this->neo->{$this->operation}($query);
        } else if ($this->db == "postgres") {
            // sending the table names with the query so we can use them to add the data properly to the cron queue
            return $this->postgres->{$this->operation}($query, array_keys($this->tables));
        }
    }

    public function insertEntry($data, $tableName, array $columns)
    {
        $tableData = $data['data'][$tableName];

        $valuesArray = array();
        $notNullColumns = array();
        $dateCreated = new \DateTime();
        $dateCreated = $dateCreated->format('c');

        if ($tableName == "invite") {
            $dateCreatedColumn = "date_issued";
        } else {
            $dateCreatedColumn = "date_created";
            //$dateModifiedColumn = "date_modified";
            if (($tableName != "analytic") && ($tableName != "track_play")) {
                $dateModifiedColumn = "date_modified";
            }
        }

        if(!isset($data['multi']) || $data['multi'] === false) {
            $tableData[$dateCreatedColumn] = $dateCreated;
            $columns[] = $dateCreatedColumn;
            if (isset($dateModifiedColumn)) {
                $tableData[$dateModifiedColumn] = $dateCreated;
                $columns[] = $dateModifiedColumn;
            }
            foreach ($columns as $column) {
                if(isset($tableData[$column]) && !is_null($tableData[$column])) {
                    $notNullColumns[] = $column;
                    $columnData = pg_escape_string($tableData[$column]);
                    if (is_string($tableData[$column])) {
                        $valuesArray[] = "'$columnData'";
                    } else {
                        $valuesArray[] = $columnData;
                    }
                }
            }
            $values = "(" . implode(", ", $valuesArray) . ")";
        } elseif ($data['multi'] == true) {
            foreach ($tableData as $i => $entry) {
                $entry[$dateCreatedColumn] = $dateCreated;
                if (isset($dateModifiedColumn)) {
                    $entry[$dateModifiedColumn] = $dateCreated;
                }
                foreach ($entry as $column => $value) {
                    if (!is_null($entry[$column])) {
                        if (!in_array($column, $notNullColumns)) {
                            $notNullColumns[] = $column;
                        }
                        $data = pg_escape_string($entry[$column]);
                        if (is_string($entry[$column])) {
                            $valuesArray[$i][] = "'$data'";
                        } else {
                            $valuesArray[$i][] = $data;
                        }
                    }
                }
            }
            $values = "";
            $count = 0;
            foreach($valuesArray as $entry) {
                $values .= "(" . implode(", ", $entry) . ")";
                if ($count < $i) {
                    $values .= ",";
                }
                $count++;
            }
        } else {
            //TODO handle exception
        }
        $params = implode(", ", $notNullColumns);
        if($tableName == 'user') {
            $tableName = 'public.' . $tableName;
        }

        $query = "INSERT INTO $tableName ($params) VALUES $values";
        $result = $this->postgres->insert($query, $tableName);
        return $result;
    }
}
