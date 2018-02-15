<?php

use \Psecio\Canary\Instance;
use \Psecio\Canary\Data;

class InstanceTest extends \PHPUnit\Framework\TestCase
{
    public function testGetSetConfig()
    {
        $config = ['test' => true];

        $instance = new Instance();
        $instance->setConfig($config);

        $this->assertEquals($config, $instance->getConfig());
    }

    public function testSetConfigInit()
    {
        $config = ['test' => true];
        $instance = new Instance($config);

        $this->assertEquals($config, $instance->getConfig());
    }

    public function testGetSpecificConfig()
    {
        $config = ['test' => true];
        $instance = new Instance($config);

        $this->assertEquals(true, $instance->getConfig('test'));
    }

    public function testGetSetData()
    {
        $value = ['test' => true];

        $data = new Data($value);
        $instance = new Instance(['data' => $value]);

        $this->assertEquals($data, $instance->getData());
    }

}
