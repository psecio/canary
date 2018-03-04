<?php

use PHPUnit\Framework\TestCase;
use Psecio\Canary\Data;

/**
 * @covers \Psecio\Canary\Data
 */
class DataTest extends TestCase
{
    public function testResolve()
    {
        $data = new Data([
            'foo' => 'bar',
        ]);

        $result = $data->resolve('foo');

        $this->assertSame('bar', $result);
    }

    public function testResolveAddedValue()
    {
        $data = new Data();
        $data->add('foo', 'bar');

        $result = $data->resolve('foo');

        $this->assertSame('bar', $result);
    }

    public function testResolveNestedValue()
    {
        $data = new Data([
            'foo' => [
                'bar' => [
                    'fiz' => 'buz',
                ],
            ],
        ]);

        $result = $data->resolve('fiz');

        $this->assertSame('buz', $result);
    }

    public function testDepthFirstKeyResolution()
    {
        $data = new Data([
            'foo' => [
                'bar' => [
                    'fiz' => 'buz',
                ],
                'fiz' => 'quux',
            ],
        ]);

        $result = $data->resolve('fiz');

        $this->assertSame('buz', $result);
    }

    public function testNotResolve()
    {
        $data = new Data();

        $result = $data->resolve('foo');

        $this->assertNull($result);
    }

    public function testFind()
    {
        $data = [
            'foo' => 'bar',
        ];

        $found = null;

        $result = (new Data())->find('foo', $data, $found);

        $this->assertTrue($result);
        $this->assertSame('bar', $found);
    }

    public function testFindNestedValue()
    {
        $data = [
            'foo' => [
                'bar' => [
                    'fiz' => 'buz',
                ],
            ],
        ];

        $found = null;

        $result = (new Data())->find('fiz', $data, $found);

        $this->assertTrue($result);
        $this->assertSame('buz', $found);
    }

    public function testNotFind()
    {
        $found = null;

        $result = (new Data())->find('foo', [], $found);

        $this->assertFalse($result);
        $this->assertNull($found);
    }

    public function testDepthFirstKeyFinding()
    {
        $data = [
            'foo' => [
                'bar' => [
                    'fiz' => 'buz',
                ],
                'fiz' => 'quux',
            ],
        ];

        $found = null;

        $result = (new Data())->find('fiz', $data, $found);

        $this->assertTrue($result);
        $this->assertSame('buz', $found);
    }
}
