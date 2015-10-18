<?php
namespace Eardish\Gateway\DataObjects\DataObjects;

use Eardish\Gateway\DataObjects\Core\MultipleLevelDataObject;
use Eardish\Exceptions\EDInvalidOrMissingParameterException;

class DataBlock extends MultipleLevelDataObject
{
    public function __construct()
    {
//        $structure = array(
//            "model" => "loggedInUser",
//            "raw" => array("name" => "TestName", "path" => "/artists/new"),
//            "action" => "null",
//            "communicationType" => "[requested|update]",
//            "profile" => array(
//                "name" => "",
//                "location" => "",
//                "genre" => ""
//            ),
//            "albums" => array(
//                "id" => "",
//                "name" => ""
//            ),
//            "tracks" => array(
//                "name" => ""
//            ),
//            "data" => array(
//                "bio" => "",
//                "website" => ""
//            )
//        );
        parent::__construct(
            null,
            'data'
        );
    }
    public function setData($options, $listType = null)
    {
        if (is_array($options)) {
            $this->setOptions($this->processDatabaseResponse($options, $listType));
        } else {
            throw new EDInvalidOrMissingParameterException("GATEWAY::DataBlock:  invalid arguments");

        }
    }

    /**
     * Convert the keys of a block of data from snake_case to camelCase
     *
     * @param $data
     * @return array
     */
    private function processDatabaseResponse($data, $listType = 'list')
    {
        if (isset($data[0])) {
            $arr = [];
            foreach ($data as $key => $entry) {
                $arr[$listType][] = $this->snakeToCamel($entry);
            }

            return $arr;
        } else {
            return $this->snakeToCamel($data);
        }
    }

    private function snakeToCamel($data)
    {
        foreach ($data as $key => $value) {
            $camel = "";
            $parts = explode("_", $key);

            foreach ($parts as $index => $word) {
                $camel .= $index ? ucfirst($word) : $word;
            }
            unset($data[$key]);

            if (is_array($value)) {
                $data[$camel] = $this->snakeToCamel($value);
            } else {
                $data[$camel] = $value;
            }
        }

        return $data;
    }
}
