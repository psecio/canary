<?php

use \Psecio\Canary\Instance;
use \Psecio\Canary\Data;
use \Psecio\Canary\Criteria;
use \Psecio\Canary\CriteriaSet;

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

    public function testBuildStatic()
    {
        $config = [
            'data' => ['test' => true]
        ];
        $i = Instance::build($config);

        $this->assertInstanceOf(Instance::class, $i);

        // The config should match
        $this->assertEquals($config, $i->getConfig());
    }

    public function testAddCriteriaSimple()
    {
        $i = Instance::build();
        $i->if('username', 'test');
        $criteria = $i->getCriteria();

        $this->assertInstanceOf(CriteriaSet::class, $criteria);
        $this->assertCount(1, $criteria);
    }

    public function testAddCriteriaSet()
    {
        $set = new CriteriaSet();
        $cinstance = new \Psecio\Canary\Criteria\Equals('username', 'test');
        $set->add($cinstance);
        
        $i = Instance::build();
        $i->if($set);

        $this->assertEquals($set, $i->getCriteria());
    }
}
