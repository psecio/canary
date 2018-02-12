<?php

namespace Psecio\Canary\Notify;

use \Psecio\Canary\Criteria;

class ErrorLog extends \Psecio\Canary\Notify
{
    public function execute(Criteria $criteria)
    {
        echo __CLASS__.' :: '.__FUNCTION__."\n";
        error_log('Canary match: '.json_encode($criteria));
    }
}
