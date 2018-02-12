<?php

namespace Psecio\Canary\Notify;

use \Psecio\Canary\Criteria;

class Callback extends \Psecio\Canary\Notify
{
    public function execute(Criteria $criteria)
    {
        $callback = $this->getConfig('callback');
        return $callback($criteria);
    }
}
