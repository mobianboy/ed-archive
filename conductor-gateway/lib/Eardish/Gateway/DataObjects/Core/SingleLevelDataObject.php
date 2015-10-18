<?php

namespace Eardish\Gateway\DataObjects\Core;

use Eardish\Gateway\Interfaces\DataObjectInterface;
use Eardish\Gateway\config\JSONLoader;

class SingleLevelDataObject implements DataObjectInterface
{
    /**
     *
     * @var array
     */
    protected $structure = array();
    /**
     *
     * @var array
     */
    protected $values = array();
    /**
     *
     * @var string
     */
    protected $name = 'UnnamedSingleLevelDataObject';

    /**
     * @var JSONLoader
     */
    protected $jsonLoader;

    /**
     *
     * @param array $structure
     * @param string $name
     */
    public function __construct($structure = null, $name = 'UnnamedSingleLevelDataObject')
    {
        $this->jsonLoader = new JSONLoader();
        $this->structure = $this->loadStructure($structure);
        $this->name = $name;
    }

    /**
     *
     * @param string $pathPart
     * @param string $fileName
     * @return array
     */
    public function loadStructure($structure = null, $pathPart = null, $fileName = null)
    {
        if (is_null($structure)) {
            $arr = explode("\\", get_class($this));
            $class = array_pop($arr);
            if (is_null($pathPart)) {
                $path = realpath(__DIR__."/../../config/DataObjects");
            } else {
                $path = realpath(__DIR__.$pathPart);
                $class = $fileName;
            }
            return $this->jsonLoader->loadJSONConfig($path."/".$class.".json");
        } else {
            if (is_array($structure)) {
                return $structure;
            }

            $path = realpath(__DIR__."/../../config/DataObjects");

            return $this->jsonLoader->loadJSONConfig($path."/".$structure.".json");
        }
    }

    /**
     *
     * @return array
     */
    public function getStructure()
    {
        return $this->structure;
    }

    /**
     * @param string $option
     * @return string
     */
    public function getOption($option)
    {
        return (isset($this->values[$option])) ? $this->values[$option] : '';
    }

    public function isOptionSettable($option)
    {
        return (isset($this->structure[$option])) ? true : false;
    }

    /**
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->values;
    }

    /**
     *
     * @param array $options
     * @return \Eardish\Gateway\DataObjects\Core\SingleLevelDataObject
     * @throws \InvalidArgumentException
     */
    public function setOptions($options = array())
    {
        if (!is_array($options)) {
            throw new \InvalidArgumentException();
        }

        foreach ($options as $option => $value) {
//            if (isset($this->structure[$option])) {
//
//            }
            $this->values[$option] = $value;
        }
        return $this;
    }

    /**
     *
     * @param string $option
     * @param string $value
     * @return \Eardish\Gateway\DataObjects\Core\SingleLevelDataObject
     * @throws \InvalidArgumentException
     */
    public function setOption($option, $value)
    {
        if (!is_string($option)) {
            throw new \InvalidArgumentException();
        }

        if (isset($this->structure[$option])) {
            $this->values[$option] = $value;
        }

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }
    /**
     *
     * @param string $name
     * @param integer[] $arguments
     * @return \Eardish\Gateway\DataObjects\Core\SingleLevelDataObject
     * @throws \BadMethodCallException
     */
    public function __call($name, $arguments)
    {
        if (strpos($name, "get") === 0 && strlen($name) > 3) {
            $option = strtolower(substr($name, 3));
            if (isset($this->structure[$option])) {
                return (isset($this->values[$option])) ? $this->values[$option] : '';
            } else {
                throw new \BadMethodCallException();
            }
        } elseif (strpos($name, "set") === 0 && strlen($name) > 3) {
            $option = strtolower(substr($name, 3));
            if (isset($this->structure[$option])) {
                $this->values[$option] = $arguments[0];
                return $this;
            } else {
                throw new \BadMethodCallException();
            }
        } else {
            throw new \BadMethodCallException();
        }
    }
}
