<?php

use \Psecio\Canary\Instance;
use \Psecio\Canary\Data;
use \Psecio\Canary\Criteria;
use \Psecio\Canary\CriteriaSet;
use \Psecio\Canary\Criteria\Equals;

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

    public function testAddSingleCriteria()
    {
        $criteria = new Equals('test', 'foo');
        $i = Instance::build()->if($criteria);

        $set = $i->getCriteria();
        $this->assertCount(1, $set);

        $c = $set[0];
        $this->assertEquals($c, $criteria);
    }

    public function testAddMultipleKeyValue()
    {
        $criteria = [
            'foo' => 'bar',
            'baz' => 'quux'
        ];
        $i = Instance::build()->if($criteria);
        $set = $i->getCriteria();

        // The values are stored in a set so this set is the single value
        $this->assertCount(1, $set);
        $this->assertCount(2, $set[0]);

        $set = $set[0];
        // Ensure that our values are the same as what we gave
        $first = $set[0]->toArray();
        $this->assertEquals('bar', $first['value']);

        $second = $set[1]->toArray();
        $this->assertEquals('quux', $second['value']);
    }

    public function testAddMultipleKeyValueListsToCriteria()
    {
        $criteria = [
            'foo' => ['bar', 'fiz', 'buz'],
            'baz' => ['quux', 'qux']
        ];
        $i = Instance::build()->if($criteria);
        $set = $i->getCriteria();

        // The values are stored in a set so this set is the single value
        $this->assertCount(1, $set);
        $this->assertCount(2, $set[0]);

        $set = $set[0];
        // Ensure that our values are the same as what we gave
        $first = $set[0]->toArray();
        $this->assertEquals(['bar', 'fiz', 'buz'], $first['value']);

        $second = $set[1]->toArray();
        $this->assertEquals(['quux', 'qux'], $second['value']);
    }

    public function testAddNotifyToLast()
    {
        $callback = function() { echo 'test'; };
        $config = [ 'callback' => $callback ];

        $notify = new \Psecio\Canary\Notify\Callback($config);
        $i = Instance::build()->if(['test' => 'foo'])->then($notify);

        $set = $i->getCriteria();
        $criteria = $set[0][0];

        $notify = $criteria->getNotify();
        // print_r($notify);
        $this->assertInstanceOf(\Psecio\Canary\Notify\Callback::class, $notify);
        $this->assertEquals($notify->getConfig('callback'), $callback);
    }

    public function testResolveNotify()
    {
        $notify = new \Psecio\Canary\Notify\Callback(['callback' => function() {}]);
        $i = Instance::build();

        $result = $i->resolveNotify($notify);
        $this->assertEquals($result, $notify);

        // Now try with a callback
        $result = $i->resolveNotify(function() { });
        $this->assertInstanceOf(\Psecio\Canary\Notify\Callback::class, $result);
    }

    /**
     * Throws an exception on a bad notify type
     *
     * @expectedException \InvalidArgumentException
     */
    public function testResolveNotifyInvalid()
    {
        $notify = (object)['foo' => 'badvalue'];

        $i = Instance::build();
        $i->resolveNotify($notify);
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage Whoops!
     */
    public function testMatchCriteriaValue()
    {
        $config = [
            'data' => [
                'foo' => 'bar',
            ],
            'notify' => function () {
                throw new \UnexpectedValueException('Whoops!');
            },
        ];

        $criteria = [
            'foo' => 'bar',
            'baz' => 'quux',
        ];

        Instance::build($config)
            ->if($criteria)
            ->execute();
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage Whoops!
     */
    public function testMatchCriteriaList()
    {
        $config = [
            'data' => [
                'foo' => 'bar',
            ],
            'notify' => function () {
                throw new \UnexpectedValueException('Whoops!');
            },
        ];

        $criteria = [
            'foo' => [
                'bar',
                'fiz',
                'buz',
            ],
            'baz' => [
                'quux',
                'qux',
            ],
        ];

        Instance::build($config)
            ->if($criteria)
            ->execute();
    }

    public function testNotMatchCriteriaList()
    {
        $config = [
            'data' => [
                'foo' => 'FizBuz',
            ],
            'notify' => function () {
                throw new \UnexpectedValueException('Whoops!');
            },
        ];

        $criteria = [
            'foo' => [
                'bar',
                'fiz',
                'buz',
            ],
            'baz' => [
                'quux',
                'qux',
            ],
        ];

        $result = Instance::build($config)
            ->if($criteria)
            ->execute();

        $this->assertFalse($result);
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage Whoops!
     */
    public function testConfigWithoutNotify()
    {
        $config = [
            'data' => [
                'foo' => 'bar',
            ],
        ];

        $criteria = [
            'foo' => 'bar',
            'baz' => 'quux',
        ];

        Instance::build($config)
            ->if($criteria)
            ->then(function () {
                throw new \UnexpectedValueException('Whoops!');
            })
            ->execute();
    }
}
