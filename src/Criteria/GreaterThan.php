<?php

namespace Psecio\Canary\Criteria;

use \Psecio\Canary\Data;

class GreaterThan extends Equals
{
    public function evaluate(Data $input)
    {
        $val = $input->resolve($this->key);

        if (is_array($this->value) || is_array($val)) {
            throw new \InvalidArgumentException('Greater Than cannot evaluate array');
        }

        return ($val > $this->value);
    }

    public function toArray() : array
    {
        return [
            'type' => 'greater-than',
            'key' => $this->key,
            'value' => $this->value
        ];
    }
}
