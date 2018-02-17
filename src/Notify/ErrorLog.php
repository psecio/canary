<?php

namespace Psecio\Canary\Notify;

use \Psecio\Canary\Criteria;

class ErrorLog extends \Psecio\Canary\Notify
{
    public function execute(Criteria $criteria)
    {
        error_log('Canary match: '.json_encode($criteria));
    }
}
