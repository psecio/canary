<?php

namespace Psecio\Canary;

abstract class Notify
{
    protected $config = [];

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function getConfig($key = null)
    {
        return ($key !== null && isset($this->config[$key])) ? $this->config[$key] : $this->config;
    }

    public abstract function execute(Criteria $criteria);
}
