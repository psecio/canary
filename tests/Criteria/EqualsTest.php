<?php

use PHPUnit\Framework\TestCase;
use Psecio\Canary\Criteria\Equals;
use Psecio\Canary\Data;
use Psecio\Canary\Notify\ErrorLog;

/**
 * @covers \Psecio\Canary\Criteria\Equals
 * @covers \Psecio\Canary\Criteria
 */
class EqualsTest extends TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testZeroConstructorParamsIsInvalid()
    {
        new Equals();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testOneConstructorParamIsInvalid()
    {
        new Equals(1234);
    }

    public function testSetNotify()
    {
        $criteria = new Equals(1234, 1234);

        $this->assertInstanceOf(ErrorLog::class, $criteria->getNotify());

        $notify = function () {

        };

        $this->assertNull($criteria->setNotify($notify));

        $this->assertSame($notify, $criteria->getNotify());
    }

    public function testGetNotify()
    {
        $criteria = new Equals(1234, 1234);

        $this->assertInstanceOf(ErrorLog::class, $criteria->getNotify());

        $notify = function () {

        };

        $this->assertNull($criteria->setNotify($notify));

        $this->assertSame($notify, $criteria->getNotify());
    }

    /**
     * @dataProvider dataEvaluate
     * @param $data
     * @param $criteriaValue
     * @param $expected
     */
    public function testEvaluate($data, $criteriaValue, $expected)
    {
        $criteria = new Equals('myField', $criteriaValue);

        $actual = $criteria->evaluate(new Data([
            'myField' => $data,
        ]));

        $this->assertSame($expected, $actual);
    }

    public function dataEvaluate()
    {
        return [
            [false, false, true],
            [true, false, false],
            [12.34, 12.34, true],
            [12.34, 43.21, false],
            [1234, 1234, true],
            [1234, 4321, false],
            [new \stdClass, new \stdClass, true],
            [new \stdClass, new \Exception, false],
            ['foo', 'foo', true],
            ['foo', 'bar', false],
            [1234, '1234', true],
            [0, false, true],
            [0.0, false, true],
            ['', false, true],
            [[], [], true],
            ['foo', ['foo'], true],
            ['foo', ['bar'], false],
            [[], [[]], true],
        ];
    }

    public function testJsonSerialize()
    {
        $criteria = new Equals('foo', 'bar');

        $result = \json_decode(\json_encode($criteria), true);

        $this->assertCount(3, $result);
        $this->assertArraySubset(
            [
                'type' => 'equals',
                'key' => 'foo',
                'value' => 'bar',
            ],
            $result
        );
    }

    public function testToArray()
    {
        $criteria = new Equals('foo', 'bar');

        $result = $criteria->toArray();

        $this->assertCount(3, $result);
        $this->assertArraySubset(
            [
                'type' => 'equals',
                'key' => 'foo',
                'value' => 'bar',
            ],
            $result
        );
    }
}
