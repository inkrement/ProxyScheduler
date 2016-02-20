<?php

namespace Inkrement\ProxyScheduler;

use InvalidArgumentException;
use Inkrement\ProxyScheduler\Adapter\AdapterInterface;
use Inkrement\ProxyScheduler\SchedulingAlgorithms\RoundRobin;
use Inkrement\ProxyScheduler\SchedulingAlgorithms\SchedulingInterface;

class ProxyScheduler
{
    private $dao;
    private $algorithm;
    private $timetowait;

    public function __construct(AdapterInterface $dao, SchedulingInterface $algorithm = null, $timetowait = 60)
    {
        if (is_null($dao)) {
            throw new InvalidArgumentException('no dao provided!');
        }

        if (is_null($algorithm)) {
            $this->algorithm = new RoundRobin();
        } else {
            $this->algorithm = $algorithm;
        }

        $this->dao = $dao;
        $this->timetowait = $timetowait;
    }
    public function getNext()
    {
        $proxies = $this->dao->getFreshProxies($this->timetowait);

        $proxy = $this->algorithm->getNext($proxies);

        if (!is_null($proxy)) {
            $this->dao->updateLastUsed($proxy, time());
        }

        return $proxy;
    }
}
