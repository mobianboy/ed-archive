<?php

namespace Eardish\Gateway\DataObjects\Core;

use Eardish\Gateway\Interfaces\DataObjectInterface;

class MultipleLevelDataObject implements DataObjectInterface
{
    /**
     * @var array
     */
    protected $structure = array();
    /**
     * @var array
     */
    protected $values = array();
    /**
     * @var string
     */
    protected $name = 'UnnamedMultipleLevelDataObject';

    /**
     *
     * @param array $structure
     * @param string $name
     */
    public function __construct($structure = array(), $name = 'UnnamedMultipleLevelDataObject')
    {
        $this->structure = $structure;
        $this->name = $name;
    }

    /**
     *
     * @return array
     */
    public function getStructure()
    {
        return $this->structure;
    }
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $option
     */
    public function getOption($option)
    {
        if (is_string($option)) {
            $path = explode(".", $option);

            $keypath = $option;
        } else {
            throw new \InvalidArgumentException();
        }

        if (!$this->isOptionSettable($keypath)) {
            throw new \InvalidArgumentException("The option '".$option."' is not a valid path.");
        }

        $values = &$this->values;

        $count = count($path);

        for ($i=0; $i<$count; $i++) {
            if ($count == ($i+1)) {
                if (isset($values[$path[$i]])) {
                    return $values[$path[$i]];
                } else {
                    return '';
                }
            } else {
                if (!isset($values[$path[$i]])) {
                    return '';
                }
                $values = &$values[$path[$i]];
            }
        }
    }

    public function isOptionSettable($path)
    {
        $path = explode(".", $path);

        $pathSize = count($path);

        $currentStruct = $this->structure;
        //print_r($currentStruct);
        for ($i=0; $i<$pathSize; $i++) {
            if (isset($currentStruct[$path[$i]])) {
                $currentStruct = $currentStruct[$path[$i]];
            } else {
                return false;
            }
        }

        return true;
    }

    public function getOptions()
    {
        return $this->values;
    }

    public function setOptions($options = array())
    {
        foreach ($options as $key => $value) {
            $this->setOption($key, $value);
        }
        return $this;
    }

    public function recurseArray($array, $builtKey = "")
    {
        $values = array();

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                if (!empty($builtKey)) {
                    $values = array_merge($values, $this->recurseArray($value, $builtKey.".".$key));
                } else {
                    $values = array_merge($values, $this->recurseArray($value, $key));
                }
            } else {
                if (!empty($builtKey)) {
                    $values[$builtKey.".".$key] = $value;
                } else {
                    $values[$key] = $value;
                }
            }
        }

        return $values;
    }

    public function setOption($option, $value)
    {
        $path = explode(".", $option);

        $values = &$this->values;

        $count = count($path);

        for ($i=0; $i<$count; $i++) {
            if ($count == ($i+1)) {
                $values[$path[$i]] = $value;
            } else {
                if (!isset($values[$path[$i]])) {
                    $values[$path[$i]] = array();
                }
                $values = &$values[$path[$i]];
            }
        }

        return $this;
    }
}
