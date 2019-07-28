<?php

use PHPUnit\Framework\TestCase;
use Psecio\Canary\Criteria\GreaterThan;
use Psecio\Canary\Data;
use Psecio\Canary\Notify\ErrorLog;

/**
 * @covers \Psecio\Canary\Criteria\GreaterThan
 * @covers \Psecio\Canary\Criteria
 */
class GreaterThanTest extends TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testZeroConstructorParamsIsInvalid()
    {
        new GreaterThan();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testOneConstructorParamIsInvalid()
    {
        new GreaterThan(1234);
    }

    public function testSetNotify()
    {
        $criteria = new GreaterThan(1234, 1235);

        $this->assertInstanceOf(ErrorLog::class, $criteria->getNotify());

        $notify = function () {

        };

        $this->assertNull($criteria->setNotify($notify));

        $this->assertSame($notify, $criteria->getNotify());
    }

    public function testGetNotify()
    {
        $criteria = new GreaterThan(1234, 1235);

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
        $criteria = new GreaterThan('myField', $criteriaValue);

        $actual = $criteria->evaluate(new Data([
            'myField' => $data,
        ]));

        $this->assertSame($expected, $actual);
    }

    public function dataEvaluate()
    {
        return [
            [3, 2, true],
            [2, 3, false],
            [2, 2, false],
            ["3", "2", true],
            ["2", "3", false],
            ["2", "2", false],
        ];
    }

    public function testJsonSerialize()
    {
        $criteria = new GreaterThan('foo', 'bar');

        $result = \json_decode(\json_encode($criteria), true);

        $this->assertCount(3, $result);
        $this->assertArraySubset(
            [
                'type' => 'greater-than',
                'key' => 'foo',
                'value' => 'bar',
            ],
            $result
        );
    }

    public function testToArray()
    {
        $criteria = new GreaterThan('foo', 'bar');

        $result = $criteria->toArray();

        $this->assertCount(3, $result);
        $this->assertArraySubset(
            [
                'type' => 'greater-than',
                'key' => 'foo',
                'value' => 'bar',
            ],
            $result
        );
    }
}
