<?php
namespace Eardish\EchoService;

use Eardish\EchoService\Core\AbstractService;
use Monolog\Logger;

class EchoService extends AbstractService
{
    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @param $params1 mixed
     * @param $params2 mixed
     * @param $params3 mixed
     * @return mixed
     */
    public function passEcho($params1, $params2, $params3) //params can be things like $userId, $name, $email, or other possible columns of a table
    {
        $config = $this->generateConfigArray(__FUNCTION__, 'select');//'select' can be replaced to update, insert, delete, etc.

        // Set values for config array to prepare to send to DB

        //  This is in every config array, and holds all info the db will need to fulfill request(table name, column name, values )
        //        ||
        //        ||
        //        ||   Pretty self explanatory. Just holds the name of the table that you would perform an action on
        //        ||        ||
        //        ||        ||
        //        ||        ||     Again, self-explanatory. Holds name of column you will be affecting
        //        ||        ||              ||
        //        ||        ||              ||
        //        ||        ||              ||        The value that will go in column stated under ['column_name']
        //        ||        ||              ||            ||
        $config['data']['table_name']['column_name'] = $params1;
        $config['data']['table_name']['column_name'] = $params2;
        $config['data']['table_name']['column_name'] = $params3;


        //results will all contain an array, with the first key usually being ["success" = > boolean] that
        //can be either true for successful or false for unsuccessful. If something like select statement is called it will return an array
        // structured [
        //              success = true,
        //              count = int
        //              data => [
        //                      0 => [
        //                              "id" => some_int,
        //                              "name" => some_name,
        //                           ]
        //                      1 = > [
        //                              "id" => some_other_int,
        //                              "name" => some_other_name,
        //                            ]
        //                      ]
        //              ]
        return $result = $this->conn->sendToDB($config);
    }

    /**
     * @param $params mixed
     * @return mixed
     */
    public function reverseEcho($params)
    {

        $config = $this->generateConfigArray(__FUNCTION__, 'select');

        $config['data']['table_name']['column_name'] = $params;

        $result = $this->conn->sendToDB($config)['data'][0]['email'];

        $reversedResult = strrev($result);

        return $reversedResult;
    }

    /**
     * @param $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }
}
