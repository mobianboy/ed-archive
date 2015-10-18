<?php
namespace Eardish\Cron;

use Eardish\Cron\CronProcessors\ProcessDeletedItems;
use Eardish\Cron\CronProcessors\ProcessModifiedItems;
use Eardish\Cron\CronProcessors\ProcessNewItems;
use Monolog\Logger;

class CronService
{

    /**
     * @var string
     */
    protected $query;

    /**
     * @var ProcessNewItems
     */
    protected $processNewItems;

    /**
     * @var ProcessModifiedItems
     */
    protected $processModifiedItems;

    /**
     * @var ProcessDeletedItems
     */
    protected $processDeletedItems;

    /**
     * @var Logger
     */
    protected $log;

    public function __construct(Logger $log, $username = 'eardish', $password = 'password', $host = 'localhost', $port = '5432', $dbname = 'eardish-cron')
    {
        $this->processNewItems = new ProcessNewItems();
        $this->processModifiedItems = new ProcessModifiedItems();
        $this->processDeletedItems = new ProcessDeletedItems();

        $this->pgConn = pg_pconnect("dbname=$dbname port=$port user=$username password=$password port=$port");

        $this->query = "UPDATE cron_queue SET status = -1 WHERE id = (select id FROM cron_queue where status = -1 ORDER BY id ASC LIMIT 1) RETURNING *;";
    }

    public function getEntryFromDB()
    {
        $resource = pg_query($this->pgConn, $this->query);

        if ($resource != false) {
            $result = pg_fetch_assoc($resource);
            $this->processEntry($result);
        }
        print "got here";
      //  $this->getEntryFromDB();
    }

    public function processEntry($entry)
    {
        $operation = $entry['operation'];

        switch ($operation) {
            case "insert":
                $this->processNewItems->processNewJobs($entry);
                break;
            case "update":
                $this->processModifiedItems->processModifiedJobs($entry);
                break;
            case "delete":
                $this->processDeletedItems->processDeletedJobs($entry);
                break;
            default:
                //TODO write to logger
                break;
        }
    }
}