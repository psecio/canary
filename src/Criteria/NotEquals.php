<?php

namespace Psecio\Canary\Criteria;

use \Psecio\Canary\Data;

class NotEquals extends Equals
{
    public function evaluate(Data $input)
    {
        $val = $input->resolve($this->key);

        if (is_array($this->value) && in_array($val, $this->value, false)) {
            return false;
        }

        return ($val != $this->value);
    }

    public function toArray() : array
    {
        return [
            'type' => 'not-equals',
            'key' => $this->key,
            'value' => $this->value
        ];
    }
}
