<?php

namespace Psecio\Canary\Notify;

use \Psecio\Canary\Criteria;

class Monolog extends \Psecio\Canary\Notify
{
    public function __construct(\Monolog\Logger $log)
    {
        parent::__construct([
            'logger' => $log
        ]);
    }

    public function execute(Criteria $criteria)
    {
        $this->getConfig('logger')->error(json_encode($criteria));
    }
}
