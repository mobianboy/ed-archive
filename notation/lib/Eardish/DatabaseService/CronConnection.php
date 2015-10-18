<?php
namespace Eardish\DatabaseService;

class CronConnection
{
    protected $sock;
    protected $pgCronConnection;

    public function __construct( $host = 'localhost', $port = '5432', $username = 'eardish', $password = 'password', $dbname = 'eardishcron')
    {
        $this->pgCronConnection = pg_pconnect('dbname='.$dbname.' port='.$port.' user='.$username.' password='.$password.' host='.$host);
    }

    /**
     * @param $operation string
     * @param $tables array
     * @param array $result
     */
    public function prepare($operation, $tables, array $result)
    {
        $data = json_encode($result);
        if (!(count($tables) == 1)) {
            print "too many tables provided";
            print "did not add to cron DB";
            //TODO log error
            return;
        }
        if (is_array($tables)) {
            $table = $tables[0];
        } else {
            $table = $tables;
        }
        $this->execute($operation, $table, $data);
    }

    public function execute($operation, $table, $data)
    {
        $query = "INSERT INTO cron_queue (label, operation, data, status) VALUES ('$table', '$operation', '$data', 0) RETURNING *;";
        $response = pg_query($this->pgCronConnection, $query);
        $result = pg_fetch_all($response);

        if(is_array($result)) {
            //TODO write successfully to logger
        }
    }
}
