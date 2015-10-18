<?php
namespace Eardish\Gateway;

use Respect\Validation\Validator as v;

class Validation
{
    protected $bool = array();

    public function validate($data, $specifications = "")
    {
        //process incoming specifications
        $specifications = strtolower($specifications);
        $specsArray = explode(",,", $specifications);

        $specsArray = $this->setKeyVal($specsArray);
        date_default_timezone_set('America/Los_Angeles');

        //send to validation processor
        $this->processValidators($data, $specsArray);

        //tally up the validations and return true or false
        return $this->resolve();
    }

    /**
     * splits array of validator commands in to array of key value pairs
     * @param $validators array
     * @return array
     */
    protected function setKeyVal($validators)
    {
        $paramsHash = array();
        foreach ($validators as $param) {
            if (strpos($param, "::")) {
                list($key, $val) = explode("::", $param);
                $paramsHash[$key] = $val;
            } else {
                //if there are no arguments to the param, it value is set to false
                $paramsHash[$param] = false;
            }
        }
        return $paramsHash;
    }

    /**
     * process arguments on strings and send them out to more detailed string analyzers
     * @param $validators array
     * @param $data mixed
     */
    protected function processValidators($data, $validators)
    {
        // iterate through each validation command and send to proper method
        foreach ($validators as $key => $value) {
            switch ($key) {
                case "length":
                    $this->length($data, $key, $value);
                    break;
                case "between":
                    $this->between($data, $key, $value);
                    break;
                case "email":
                    $this->email($data, $key);
                    break;
                case "date":
                    $this->date($data, $key);
                    break;
                case "leap":
                    $this->leap($data, $key);
                    break;
                case "int":
                case "integer":
                case "numeric":
                    $this->int($data, $key);
                    break;
                case "even":
                case "odd":
                    $this->parity($data, $key);
                    break;
                case "positive":
                case "negative":
                    $this->sign($data, $key);
                    break;
                case "alnum":
                    $this->alnum($data, $key);
                    break;
                case "string":
                    $this->string($data, $key);
                    break;
                case "nowhitespace":
                    $this->noWhitespace($data, $key);
                    break;
                case "notempty":
                    $this->notEmpty($data, $key);
                    break;
                case "bool":
                    $this->bool($data, $key);
                    break;
                default:
                    // no known key value found
                    $this->bool[$key] = false;
                    break;
            }
        }
    }

    // String Validators
    protected function length($data, $key, $value)
    {
        if (strpos($value, "&&")) {
            $value = explode("&&", $value);
            $this->bool[$key] = v::string()->length($value[0], $value[1])->validate($data);
        } elseif (strpos($value, "||")) {
            $value = explode("||", $value);
            $this->bool[$key] = v::string()->length($value[0], $value[1], false)->validate($data);
        }
    }

    protected function between($data, $key, $value)
    {
        if (strpos($value, "&&")) {
            $value = explode("&&", $value);
            $this->bool[$key] = v::oneOf(v::int(), v::date(), v::float())->between($value[0], $value[1], true)->validate($data);
        } elseif (stripos($value, "||")) {
            $value = explode("||", $value);
            $this->bool[$key] = v::oneOf(v::int(), v::date(), v::float())->between($value[0], $value[1])->validate($data);
        }
    }

    protected function alnum($data, $key)
    {
        $this->bool[$key] = v::alnum()->validate($data);
    }

    protected function string($data, $key)
    {
        $this->bool[$key] = v::string()->validate($data);
    }

    protected function noWhitespace($data, $key)
    {
        $this->bool[$key] = v::oneOf(v::alnum(), v::string())->noWhitespace()->validate($data);
    }

    protected function notEmpty($data, $key)
    {
        $this->bool[$key] = v::oneOf(v::int(), v::float(), v::date(), v::string(), v::alnum())->notEmpty()->validate($data);
    }

    protected function date($data, $key)
    {
        $this->bool[$key] = v::date()->validate($data);
    }

    protected function leap($data, $key)
    {
        $this->bool[$key] = v::leapYear()->validate($data);
    }

    // Number Validators
    protected function int($data, $key)
    {
        $this->bool[$key] = v::int()->validate($data);
    }

    protected function parity($data, $key)
    {
        if ($key == "even") {
            $this->bool[$key] = v::even()->validate($data);
        } else {
            $this->bool[$key] = v::odd()->validate($data);
        }
    }

    protected function sign($data, $key)
    {
        if ($key == "positive") {
            $this->bool[$key] = v::positive()->validate($data);
        } else {
            $this->bool[$key] = v::negative()->validate($data);
        }
    }

    // Bool Validation
    protected function bool($data, $key)
    {
        $this->bool[$key] = v::bool()->validate($data);
    }

    // Email Validation
    protected function email($data, $key)
    {
        $this->bool[$key] = v::email()->validate($data);
    }

    // Tally up the results from the bool array
    protected function resolve()
    {
        if (count($this->bool) == 0) {
            return null;
            //data present in bools to evaluate
        } else {
            if (in_array(false, $this->bool)) {
                $this->bool = array();
                return false;
            } else {
                $this->bool = array();
                return true;
            }
        }
    }
}
