<?php
namespace Eardish;

class AppConfig
{
    protected $default;
    protected $local;
    protected $dev;
    protected $qa;
    protected $staging;
    protected $production;

    protected $environment;

    public function __construct($configFile, $environment)
    {
        $configContents = file_get_contents($configFile);
        $config = json_decode($configContents, true);

        $this->default      = $this->recurseArray($config['default']);
        $this->local        = $this->recurseArray($config['local']);
        $this->dev          = $this->recurseArray($config['dev']);
        $this->qa           = $this->recurseArray($config['qa']);
        $this->staging      = $this->recurseArray($config['staging']);
        $this->production   = $this->recurseArray($config['production']);

        $this->environment = $environment;
    }

    public function get($value)
    {
        if (isset($this->{$this->environment}[$value])) {
            return $this->{$this->environment}[$value];
        } else {
            return $this->default[$value];
        }
    }

    public function getDefault($value)
    {
        if (array_key_exists($value, $this->default)) {
            return $this->default['value'];
        }
    }

    protected function recurseArray($array, $builtKey = '')
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
}