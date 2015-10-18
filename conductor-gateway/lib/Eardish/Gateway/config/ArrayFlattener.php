<?php
namespace Eardish\Gateway\config;

class ArrayFlattener
{
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
}