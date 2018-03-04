<?php

use PHPUnit\Framework\TestCase;
use Psecio\Canary\Criteria\Equals;
use Psecio\Canary\CriteriaSet;
use Psecio\Canary\Notify\Callback;
use Psecio\Canary\Notify\ErrorLog;

/**
 * @covers \Psecio\Canary\CriteriaSet
 */
class CriteriaSetTest extends TestCase
{
    public function testArrayAccess()
    {
        $criteria = new Equals('myField', 1234);
        $criteriaSet = new CriteriaSet();

        $this->assertFalse($criteriaSet->offsetExists(0));
        $criteriaSet->offsetSet(0, $criteria);
        $this->assertTrue($criteriaSet->offsetExists(0));
        $this->assertSame($criteria, $criteriaSet->offsetGet(0));
        $criteriaSet->offsetUnset(0);
        $this->assertFalse($criteriaSet->offsetExists(0));

        $this->assertFalse(isset($criteriaSet[1]));
        $criteriaSet[1] = $criteria;
        $this->assertTrue(isset($criteriaSet[1]));
        $this->assertSame($criteria, $criteriaSet[1]);
        unset($criteriaSet[1]);
        $this->assertFalse(isset($criteriaSet[1]));
    }

    public function testIterator()
    {
        $firstCriteria = new Equals('myField', 1234);
        $secondCriteria = new Equals('myField', 2345);
        $criteriaSet = new CriteriaSet([
            $firstCriteria,
            $secondCriteria,
        ]);
        
        $this->assertTrue($criteriaSet->valid());
        $this->assertSame(0, $criteriaSet->key());
        $this->assertSame($firstCriteria, $criteriaSet->current());

        $criteriaSet->next();
        $this->assertTrue($criteriaSet->valid());
        $this->assertSame(1, $criteriaSet->key());
        $this->assertSame($secondCriteria, $criteriaSet->current());

        $criteriaSet->next();
        $this->assertFalse($criteriaSet->valid());

        $criteriaSet->rewind();
        $this->assertTrue($criteriaSet->valid());
        $this->assertSame(0, $criteriaSet->key());
        $this->assertSame($firstCriteria, $criteriaSet->current());
    }

    public function testCountable()
    {
        $criteriaSet = new CriteriaSet();

        $this->assertCount(0, $criteriaSet);

        $criteriaSet[] = new Equals('myField', 1234);

        $this->assertCount(1, $criteriaSet);
    }

    public function testAdd()
    {
        $criteria = new Equals('myField', 1234);

        $criteriaSet = new CriteriaSet();
        $criteriaSet->add($criteria);

        $this->assertSame($criteria, $criteriaSet->current());
    }

    public function testGetMatchType()
    {
        $this->assertSame(
            CriteriaSet::MATCH_ANY,
            (new CriteriaSet())->getMatchType()
        );

        $this->assertSame(
            CriteriaSet::MATCH_ALL,
            (new CriteriaSet([], CriteriaSet::MATCH_ALL))->getMatchType()
        );
    }

    public function testSetNotify()
    {
        $criteria = new Equals('myField', 1234);

        $this->assertInstanceOf(ErrorLog::class, $criteria->getNotify());

        $criteriaSet = new CriteriaSet([$criteria]);

        $callback = new Callback([
            'callback' => function () {

            },
        ]);

        $criteriaSet->setNotify($callback);

        $this->assertSame($callback, $criteria->getNotify());
    }
}
