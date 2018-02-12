<?php

namespace Psecio\Canary;

class Data
{
    private $data = [];

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function add($key, $value)
    {
        $this->data[$key] = $value;
    }

    public function resolve($key)
    {
        $found = null;
        $result = $this->find($key, $this->data, $found);
        return $found;
    }

    public function find($key, $data, &$found)
    {
        foreach ($data as $index => $value) {
            // If the key we're looking for matches, return
            if ($index == $key) {
                $found = $value;
                return true;

            } else {
                // If not, see if the value is an array so we can iterate
                if (is_array($value)) {
                    $this->find($key, $value, $found);
                    if ($found !== null) {
                        return true;
                    }
                }
            }
        }
        return false;
    }
}
