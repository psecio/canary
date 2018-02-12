<?php

namespace Psecio\Canary;

class CriteriaSet implements \ArrayAccess, \Iterator, \Countable
{
    protected $criteria = [];
    private $notify = [];
    private $index = 0;

    public function __construct(array $criteria = [])
    {
        $this->criteria = $criteria;
    }

    public function add(Criteria $criteria)
    {
        $this->criteria[] = $criteria;
    }

    public function offsetExists($offset)
    {
        return isset($this->criteria[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->criteria[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->criteria[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        if (isset($this->criteria[$offset])) {
            unset($this->criteria[$offset]);
        }
    }

    public function current()
    {
        return $this->criteria[$this->index];
    }

    public function rewind()
    {
        $this->index = 0;
    }

    public function next()
    {
        ++$this->index;
    }

    public function valid()
    {
        return isset($this->criteria[$this->index]);
    }

    public function key()
    {
        return $this->index;
    }

    public function count()
    {
        return count($this->criteria);
    }

}
