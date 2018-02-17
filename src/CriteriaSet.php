<?php

namespace Psecio\Canary;

class CriteriaSet implements \ArrayAccess, \Iterator, \Countable
{
    /**
     * Current set of criteria objects
     * @var array
     */
    protected $criteria = [];

    /**
     * Internal index location
     * @var integer
     */
    private $index = 0;

    /**
     * Match type on the set of criteria (all or any)
     * @var [type]
     */
    private $matchType = 'any';

    /**
     * Constant for "all" matching
     * @var string
     */
    const MATCH_ALL = 'all';

    /**
     * Constant for "any" matching
     * @var string
     */
    const MATCH_ANY = 'any';

    /**
     * Initialize the object with criteria and matching type
     *
     * @param array $criteria Set of criteria
     * @param string $matchType Macthing type ("any" or "all")
     */
    public function __construct(array $criteria = [], $matchType = self::MATCH_ANY)
    {
        $this->criteria = $criteria;
        $this->matchType = $matchType;
    }

    /**
     * Add a new criteria to the set
     *
     * @param \Psecio\Canary\Criteria $criteria Criteria instance
     */
    public function add($criteria)
    {
        $this->criteria[] = $criteria;
    }

    /**
     * Check for offset (ArrayAccess)
     *
     * @param integer $offset Offset to check
     */
    public function offsetExists($offset)
    {
        return isset($this->criteria[$offset]);
    }

    /**
     * Get an offset (ArrayAccess)
     *
     * @param integer $offset Offset to get
     */
    public function offsetGet($offset)
    {
        return $this->criteria[$offset];
    }

    /**
     * Set an offset
     *
     * @param integer $offset Offset location
     * @param mixed $value Value to set
     */
    public function offsetSet($offset, $value)
    {
        $this->criteria[$offset] = $value;
    }

    /**
     * Remove an offset
     *
     * @param integer $offset Integer offset
     */
    public function offsetUnset($offset)
    {
        if (isset($this->criteria[$offset])) {
            unset($this->criteria[$offset]);
        }
    }

    /**
     * Return the current value (Iterator)
     *
     * @return mixed Value at the current location
     */
    public function current()
    {
        return $this->criteria[$this->index];
    }

    /**
     * Rewind the internal index to the base value (Iterator)
     */
    public function rewind()
    {
        $this->index = 0;
    }

    /**
     * Advance the internal index to the next value (Iterator)
     */
    public function next()
    {
        ++$this->index;
    }

    /**
     * Check to see if the value at the current index exists (Iterator)
     *
     * @return boolean Index exists/doesn't exist
     */
    public function valid()
    {
        return isset($this->criteria[$this->index]);
    }

    /**
     * Return the current index value (Iterator)
     *
     * @return integer Index value
     */
    public function key()
    {
        return $this->index;
    }

    /**
     * Return the count of the current objects (Countable)
     *
     * @return integer Count of objects
     */
    public function count()
    {
        return count($this->criteria);
    }

    /**
     * Get the match type value
     *
     * @return string Match type setting
     */
    public function getMatchType()
    {
        return $this->matchType;
    }

    /**
     * Set the same notification type on all criteria
     *
     * @param \Psecio\Canary\Notify $notify Notify object instance
     */
    public function setNotify($notify)
    {
        foreach ($this->criteria as $criteria) {
            $criteria->setNotify($notify);
        }
    }

}
