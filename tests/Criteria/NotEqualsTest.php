<?php

use PHPUnit\Framework\TestCase;
use Psecio\Canary\Criteria\NotEquals;
use Psecio\Canary\Data;
use Psecio\Canary\Notify\ErrorLog;

/**
 * @covers \Psecio\Canary\Criteria\NotEquals
 * @covers \Psecio\Canary\Criteria
 */
class NotEqualsTest extends TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testZeroConstructorParamsIsInvalid()
    {
        new NotEquals();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testOneConstructorParamIsInvalid()
    {
        new NotEquals(1234);
    }

    public function testSetNotify()
    {
        $criteria = new NotEquals(1234, 1235);

        $this->assertInstanceOf(ErrorLog::class, $criteria->getNotify());

        $notify = function () {

        };

        $this->assertNull($criteria->setNotify($notify));

        $this->assertSame($notify, $criteria->getNotify());
    }

    public function testGetNotify()
    {
        $criteria = new NotEquals(1234, 1235);

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
        $criteria = new NotEquals('myField', $criteriaValue);

        $actual = $criteria->evaluate(new Data([
            'myField' => $data,
        ]));

        $this->assertSame($expected, $actual);
    }

    public function dataEvaluate()
    {
        return [
            [false, false, false],
            [true, false, true],
            [12.34, 12.34, false],
            [12.34, 43.21, true],
            [1234, 1234, false],
            [1234, 4321, true],
            [new \stdClass, new \stdClass, false],
            [new \stdClass, new \Exception, true],
            ['foo', 'foo', false],
            ['foo', 'bar', true],
            [1234, '1234', false],
            [0, false, false],
            [0.0, false, false],
            ['', false, false],
            [[], [], false],
            ['foo', ['foo'], false],
            ['foo', ['bar'], true],
            [[], [[]], false],
        ];
    }

    public function testJsonSerialize()
    {
        $criteria = new NotEquals('foo', 'bar');

        $result = \json_decode(\json_encode($criteria), true);

        $this->assertCount(3, $result);
        $this->assertArraySubset(
            [
                'type' => 'not-equals',
                'key' => 'foo',
                'value' => 'bar',
            ],
            $result
        );
    }

    public function testToArray()
    {
        $criteria = new NotEquals('foo', 'bar');

        $result = $criteria->toArray();

        $this->assertCount(3, $result);
        $this->assertArraySubset(
            [
                'type' => 'not-equals',
                'key' => 'foo',
                'value' => 'bar',
            ],
            $result
        );
    }
}
