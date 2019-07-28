<?php

namespace Psecio\Canary\Criteria;

use \Psecio\Canary\Data;

class LessThan extends Equals
{
    public function evaluate(Data $input)
    {
        $val = $input->resolve($this->key);

        if (is_array($this->value) || is_array($val)) {
            throw new \InvalidArgumentException('Less Than cannot evaluate array');
        }

        return ($val < $this->value);
    }

    public function toArray() : array
    {
        return [
            'type' => 'less-than',
            'key' => $this->key,
            'value' => $this->value
        ];
    }
}
