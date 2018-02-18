<?php

namespace Psecio\Canary\Notify;

use \Psecio\Canary\Criteria;

class Pagerduty extends \Psecio\Canary\Notify
{
    public function __construct(\PagerDuty\Event $event)
    {
        parent::__construct([
            'event' => $event
        ]);
    }

    public function execute(Criteria $criteria)
    {
        $event = $this->getConfig('event');
        $event->setDescription('Canary triggered')
            ->setDetails($criteria->toArray());

        $resp = $event->trigger();
    }
}
