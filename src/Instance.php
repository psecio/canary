<?php

namespace Psecio\Canary;

use \Psecio\Canary\Criteria\Equals;
use Psecio\Canary\Canary;

class Instance
{
    private $data;
    private $config = [];
    private $criteria;

    public function __construct(array $config = [])
    {
        $this->setConfig($config);

        if (isset($config['data'])) {
            $this->data = new Data($config['data']);
        } else {
            $this->data = new Data($this->getDefaultData());
        }
    }

    public static function build(array $config = [])
    {
        $instance = new \Psecio\Canary\Instance($config);
        return $instance;
    }

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

    protected function loadGlobals()
    {
        $this->addData('get', $_GET);
        $this->addData('post', $_POST);
    }
    protected function loadRequest()
    {
        $this->addData('request', [
            'uri' => $_SERVER['REQUEST_URI']
        ]);
    }

    protected function setConfig(array $config)
    {
        $this->config = $config;
    }
    protected function getConfig($key = null)
    {
        return ($key !== null && isset($this->config[$key])) ? $this->config[$key] : $this->config;
    }

    protected function addData($key, $value)
    {
        $this->data[$key] = $value;
    }
    protected function getData($key = null)
    {
        return ($key !== null && isset($this->data[$key])) ? $this->data[$key] : $this->data;
    }

    public function if(...$params) : \Psecio\Canary\Instance
    {
        if (count($params) == 1 && $params[0] instanceof CriteraSet) {
            $this->criteria = $params[0];

        } elseif (count($params) > 1) {
            if ($this->criteria == null) {
                $this->criteria = new CriteriaSet();
            }

            // Add it as an "equals" criteria
            $equals = new Equals($params[0], $params[1]);
            $this->criteria->add($equals);
        }
        
        return $this;
    }

    public function then($callback) : \Psecio\Canary\Instance
    {
        if (is_callable($callback)) {
            $callback = new Notify\Callback(['callback' => $callback]);
        } elseif (!($callback instanceof Notify)) {
            throw new \InvalidArgumentException('Invalid notification method');
        }

        // If they call this, get the last criteria on the stack and update the notify
        $last = count($this->criteria) - 1;
        $this->criteria[$last]->setNotify($callback);

        return $this;
    }

    public function execute() : bool
    {
        $match = false;
        foreach ($this->criteria as $criteria) {
            if ($criteria->evaluate($this->data) === true) {
                $notify = $criteria->getNotify();

                echo "Notify: ".get_class($notify)."\n";

                $notify->execute($criteria);
                $match = true;
            }
        }

        return $match;
    }
}
