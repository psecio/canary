<?php

use PHPUnit\Framework\TestCase;
use Psecio\Canary\Criteria\Equals;
use Psecio\Canary\Notify\Callback;

/**
 * @covers \Psecio\Canary\Notify\Callback
 * @covers \Psecio\Canary\Notify
 */
class CallbackTest extends TestCase
{
    public function testGetFullConfig()
    {
        $notify = new Callback([
            'foo' => 'bar',
            'baz' => 'quux',
        ]);

        $config = $notify->getConfig();

        $this->assertCount(2, $config);
        $this->assertArraySubset(
            [
                'foo' => 'bar',
                'baz' => 'quux',
            ],
            $config
        );
    }

    public function testGetConfigByKey()
    {
        $notify = new Callback([
            'foo' => 'bar',
            'baz' => 'quux',
        ]);

        $config = $notify->getConfig('foo');

        $this->assertEquals('bar', $config);
    }

    public function testGetFullConfigOnKeyMiss()
    {
        $notify = new Callback([
            'foo' => 'bar',
            'baz' => 'quux',
        ]);

        $config = $notify->getConfig('FizBuz');

        $this->assertCount(2, $config);
        $this->assertArraySubset(
            [
                'foo' => 'bar',
                'baz' => 'quux',
            ],
            $config
        );
    }

    public function testExecute()
    {
        $expected = new \stdClass();

        $notify = new Callback([
            'callback' => function () use ($expected) {
                return $expected;
            },
        ]);

        $actual = $notify->execute(new Equals('myField', 1234));

        $this->assertSame($expected, $actual);
    }
}
