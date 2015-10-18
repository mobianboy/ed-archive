<?php
namespace Eardish\DatabaseService\DatabaseControllers\SeedControllers;

use Eardish\DatabaseService\DatabaseControllers\PostgresController;

class PostgresSeedController extends PostgresController
{
    public function insertSeed($table, $seed)
    {
        pg_insert($this->pgConn, $table, $seed);
    }

    public function selectSeed($table, $values)
    {
        $results =  pg_select($this->pgConn, $table, $values);

        if (array_keys($values)[0] == 'id') {
            return $results[0]['id'];
        } else {
            return $results;
        }
    }

    public function updateSeed($table, $seed, $where)
    {
        pg_update($this->pgConn, $table, $seed, $where);
    }

    public function querySeed($query)
    {
        return pg_query($this->pgConn, $query);
    }
}
