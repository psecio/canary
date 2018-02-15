<?php

namespace Psecio\Canary\Notify;

use \Psecio\Canary\Criteria;

class Slack extends \Psecio\Canary\Notify
{
    const NAME = 'Canary Agent';

    public function __construct(\Maknz\Slack\Client $client)
    {
        parent::__construct([
            'client' => $client
        ]);
    }

    public function execute(Criteria $criteria)
    {
        $client = $this->getConfig('client');
        $client->from(self::NAME)
            ->enableMarkdown()
            ->send('*Canary triggered:* '.json_encode($criteria));
    }
}
