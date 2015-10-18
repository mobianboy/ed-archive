<?php

namespace Eardish\Gateway\Interfaces;

interface FormatFactoryInterface
{
    /**
     * @return string
     */
    public function buildFullExport($compiledArray);

    public function buildPartialExport($rootElement, $dataArray);

    public function buildSingleExport($name, $value);

    public function openTag();

    public function closeTag();

    public function headers();
}
