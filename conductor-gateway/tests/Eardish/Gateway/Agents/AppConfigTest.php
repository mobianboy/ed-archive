<?php

namespace Eardish\Gateway\Agents;

/**
 * @codeCoverageIgnore
 */
class AppConfigTest extends \PHPUnit_Framework_TestCase
{

    protected $appConfig;

    public function setUp()
    {
        $configFile = realpath(dirname(__DIR__). '/../../..') . '/app.json';

        $this->appConfig = new \Eardish\AppConfig($configFile, "local");
    }

    public function test()
    {
        $this->assertEquals(2+2, 4);
    }

}