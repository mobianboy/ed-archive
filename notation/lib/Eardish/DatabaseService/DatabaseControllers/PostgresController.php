<?php
namespace Eardish\DatabaseService\DatabaseControllers;

use Doctrine\ORM\Query;
use Eardish\DatabaseService\CronConnection;
use Eardish\DatabaseService\DatabaseControllers\Models;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

use \Eardish\Exceptions\EDException;

class PostgresController
{
    /**
     * @var EntityManager
     */
    protected $entityManager;
    protected $query;
    protected $conn;
    protected $pgConn;

    protected $cronConnection;

    public function __construct(CronConnection $cronConnection, $host = 'localhost', $port = '5432', $username = 'eardish', $password = 'password', $dbname = 'eardish')
    {
        $conn = array(
            'driver' => 'pdo_pgsql',
            'user'   => $username,
            'password' => $password,
            'dbname' => $dbname,
            'port' => $port,
            'host' => $host
        );

        $this->cronConnection = $cronConnection;

        //connect to postgres directly to send queries
        $params = 'dbname='.$dbname.' port='.$port.' user='.$username.' password='.$password.' host='.$host;
        $this->pgConn = pg_pconnect($params);

        $paths = array(__DIR__.'/Models');
        $config = Setup::createAnnotationMetadataConfiguration($paths, false);
        $this->entityManager = EntityManager::create($conn, $config);
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function newQueryBuilder()
    {
        return $this->entityManager->createQueryBuilder();
    }

    public function execute($query)
    {
        // returns postgres resource from query result
        $resource = pg_query($this->pgConn, $query);
        if($resource === false) {
            throw new EDException("NOTATIONPGC: null resource returned from pg_query");
        }
        //convert resource to array
        $results = (pg_fetch_all($resource));

        // results will be false if query successfully ran but there is no matching data
        if (!$results) {
            return $this->generateResultArray(true, array());
        }

        return $this->generateResultArray(true, $results);
    }

    /**
     * @param $success boolean
     * @param $data array
     * @return array
     */
    public function generateResultArray($success, $data = array())
    {
        $returnData['success'] = $success;
        $returnData['count'] = count($data);
        $returnData['data'] = $data;

        return $returnData;
    }

    public function select($query, $tables = array())
    {
        //tables not needed for select query
        return $this->execute($query);
    }

    /**
     * @param $query string
     * @param $tables
     * @return mixed
     */
    public function insert($query, $tables)
    {
        //returns inserted results to be processed by cron
        $query = $query . ' RETURNING *';
        $result = $this->execute($query);

        if ($result['success']) {
            $this->cronConnection->prepare(__FUNCTION__, $tables, $result['data']);
        }

        return $result;
    }

    public function update($query, $tables)
    {
        //returns updated results to be processed by cron
        $query = $query . ' RETURNING *';
        $result = $this->execute($query);
        if ($result['success']) {
            $this->cronConnection->prepare(__FUNCTION__, $tables, $result['data']);
        }

        return $result;
    }

    public function delete($query, $tables)
    {
        //returns deleted results to be processed by cron
        $query = $query . ' RETURNING *';
        $result = $this->execute($query);
        if ($result['success']) {
            $this->cronConnection->prepare(__FUNCTION__, $tables, $result['data']);
        }

        return $result;
    }

    public function updateBuilder($model, $columns, $updateData, $whereTable, $whereColumns, $from, $fromWhere, $clientData)
    {
        $modelAlias = "";
        if ($from) {
            $modelAlias = 'model';
        }

        $modelName = $this->transformName($model);
        $query = $this->newQueryBuilder()->update('Eardish\\DatabaseService\\DatabaseControllers\\Models\\'.$modelName);
        $columnCount = count($columns);
        for ($i = 0; $i < $columnCount; $i++) {
            if (is_bool($updateData[$i])) {
                $data = $updateData[$i]? 'true': 'false';
            } else {
                $data = pg_escape_string($this->pgConn, $updateData[$i]);
            }
            $query->set($columns[$i], "'". $data. "'");
        }
        //Sets date_modified to current
        if ($model == "invite") {
            $dateModifiedColumn = "date_redeemed";
        } else {
            $dateModifiedColumn = "date_modified";
        }
        $dateUpdate = new \DateTime();
        $dateUpdate = $dateUpdate->format('c');
        $query->set($dateModifiedColumn, "'$dateUpdate'");


        foreach ($whereColumns as $index => $whereColumn) {
            $whereData = $clientData['data'][$whereTable][$whereColumn];
            if ($index == 0) {
                $query->where($whereColumn. "=" . "'" . pg_escape_string($whereData) . "'");
            } else {
                $query->andWhere($whereColumn. "=" . "'" . pg_escape_string($whereData) . "'");
            }
        }

        if ($from) {
            $aliases = array_combine(range(0,25), range('a','z'));
            $fromString = "";
            $fromCount = count($from);
            foreach($from as $index => $fromTable) {
                $fromAlias = $aliases[$index];
                foreach ($fromWhere as $pair => $fromWherePair) {
                    $whereString = [];
                    foreach ($fromWherePair as $table => $value)
                        if ($table == $model) {
                            $whereString[$pair][] = $modelAlias . "." . $value;
                        } elseif ($table == $fromTable) {
                            $whereString[$pair][] = $fromAlias . "." . $value;
                        } else {
                            throw new EDException('could not match update query in join');
                        }
                    $query->andWhere($whereString[$pair][0] . " = " . $whereString[$pair][1]);
                }
                if ($index = $fromCount) {
                    $fromString = $fromTable . " " . $fromAlias;
                } else {
                    $fromString = $fromTable . " " . $fromAlias . ", ";
                }
            }
        }

        $dql = $query->getDQL();

        $parts = explode("SET", $dql);
        if ($model == 'user') {
            $model = "public." . $model;
        }
        if ($from) {
            $model = $model . " " . $modelAlias;
            $whereStatement = explode("WHERE", $parts[1]);
            $builtQuery = "UPDATE " . $model . " SET " . $whereStatement[0] . " FROM " . $fromString . " WHERE " .  $whereStatement[1];
        } else {
            $builtQuery = "UPDATE " . $model . " SET " . $parts[1];
        }

        $this->entityManager->clear();

        return $builtQuery;
    }

    // turns snake case into class case
    public function transformName($table)
    {
        if (strpos($table, "_") !== false) {
            $words = explode("_", $table);
            $pieces = [];
            foreach ($words as $word) {
                $pieces[] = ucfirst($word);
            }
            $finalString = implode("", $pieces);

            return $finalString;
        } else {
            return ucfirst($table);
        }
    }

    public function getEntityManager()
    {
        return $this->entityManager;
    }
}
