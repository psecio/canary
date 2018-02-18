<?php

namespace Psecio\Canary\Criteria;

use \Psecio\Canary\Data;

class Equals extends \Psecio\Canary\Criteria
{
    protected $key;
    protected $value;

    public function __construct(...$params)
    {
        if (count($params) !== 2) {
            throw new \InvalidArgumentException('Equals evaluation requires two values');
        }
        $this->key = $params[0];
        $this->value = $params[1];

        parent::__construct($params);
    }

    public function evaluate(Data $input)
    {
        $val = $input->resolve($this->key);
        return ($val == $this->value);
    }

    public function jsonSerialize() : array
    {
        return $this->toArray();
    }

    public function toArray() : array
    {
        return [
            'type' => 'equals',
            'key' => $this->key,
            'value' => $this->value
        ];
    }
}
