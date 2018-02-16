<?php

namespace Psecio\Canary;

use Psecio\Canary\Criteria\Equals;
use Psecio\Canary\Canary;
use Psecio\Canary\CriteriaSet;
use Psecio\Canary\Criteria;
use Psecio\Canary\Notify\ErrorLog;

class Instance
{
    /**
     * Current Data instance
     * @var Psecio\Canary\Data
     */
    private $data;

    /**
     * Current configuration options
     * @var array
     */
    protected $config = [
        'notify' => null
    ];

    /**
     * Current CriteraSet instance
     * @var Psecio\Canary\CriteriaSet
     */
    private $criteria;

    /**
     * Initialize the object with the provided configuration
     *
     * @param array $config Configuration options [optional]
     */
    public function __construct(array $config = [])
    {
        if (!empty($config)) {
            $this->setConfig($config);
        }
        $this->criteria = new CriteriaSet();

        if (isset($config['data'])) {
            $this->data = new Data($config['data']);
        } else {
            $this->data = new Data($this->getDefaultData());
        }
    }

    /**
     * Static method to create a new instance
     *
     * @param array $config Configuration options [optional]
     * @return \Psecio\Canary\Instance instance
     */
    public static function build(array $config = []) : \Psecio\Canary\Instance
    {
        $instance = new \Psecio\Canary\Instance($config);
        return $instance;
    }

    /**
     * Loads the default data, pulling in $_GET, $_POST and some $_SERVER values
     *
     * @return array Set of default data
     */
    protected function getDefaultData()
    {
        return [
            'get' => $_GET,
            'post' => $_POST,
            'request' => [
                'uri' => (isset($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : null
            ]
        ];
    }

    /**
     * Return the current Data instance
     *
     * @return \Psecio\Canary\Data instance
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set the current configuration options
     *
     * @param array $config Configuration options
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
    }

    /**
     * Get the current configuration. If a key is provided and exists, return the value
     *
     * @param string $key Key to locate [optional]
     * @return mixed Returns either the value if found or all configuration options
     */
    public function getConfig($key = null)
    {
        return ($key !== null && array_key_exists($key, $this->config)) ? $this->config[$key] : $this->config;
    }

    /**
     * Get the current set of Criteria
     *
     * @return \Psecio\Canary\CriteriaSet
     */
    public function getCriteria()
    {
        return $this->criteria;
    }

    /**
     * Set up a new criteria based on the input. If a CriteriaSet object is
     * provided, set the current critera value to that.
     *
     * If the method is called with two paramaters, create a simple Equals criteria
     *
     * @param mixed $params Options to use for criteria
     * @return \Psecio\Canary\Instance instance
     */
    public function if(...$params) : \Psecio\Canary\Instance
    {
        if (count($params) == 1 && $params[0] instanceof CriteriaSet) {
            $this->criteria = $params[0];
        } elseif (count($params) == 1 && $params[0] instanceof Criteria) {
            $this->criteria->add($params[0]);

        } elseif (count($params) > 1) {
            // Add it as an "equals" criteria
            $equals = new Equals($params[0], $params[1]);
            $this->criteria->add($equals);
        }

        return $this;
    }

    /**
     * Set up a notification handler. This notification will be assigned
     * to the last criteria added.
     *
     * @param mixed $callback Input, either a callable or an instance of a Notify object
     *
     * @return \Psecio\Canary\Instance instance
     */
    public function then($callback) : \Psecio\Canary\Instance
    {
        // If they call this, get the last criteria on the stack and update the notify
        $last = count($this->criteria) - 1;
        $this->criteria[$last]->setNotify($callback);

        return $this;
    }

    /**
     * Resolve the notification if it's not already a Notify instance
     *
     * @param mixed $notify Notification method
     * @return \Psecio\Canary\Notify instance
     */
    public function resolveNotify($notify)
    {
        if ($notify instanceof Notify) {
            return $notify;
        }

        if (is_callable($notify)) {
            $notify = new Notify\Callback(['callback' => $notify]);

        } elseif ($notify instanceof \Monolog\Logger) {
            $notify = new \Psecio\Canary\Notify\Monolog($notify);

        } elseif ($notify instanceof \Maknz\Slack\Client) {
            $notify = new \Psecio\Canary\Notify\Slack($notify);

        } else {
            throw new \InvalidArgumentException('Invalid notification method: '.get_class($notify));
        }
        return $notify;
    }

    /**
     * Execute the criteria on the current set of data
     *
     * @return bool Pass/fail status of evaluation
     */
    public function execute() : bool
    {
        $match = false;
        $defaultNotify = $this->getConfig('notify');

        foreach ($this->criteria as $index => $criteria) {
            if ($criteria->evaluate($this->getData()) === true) {
                $matches[$index] = true;

                // If we've been given a default notify handler, always use that
                $notify = ($defaultNotify !== null) ? $defaultNotify : $criteria->getNotify();

                // If it's not a Notify already, resolve it
                if (!($notify instanceof Notify)) {
                    $notify = $this->resolveNotify($notify);
                }

                $notify->execute($criteria);
                $match = true;
            }
        }

        return $match;
    }
}
