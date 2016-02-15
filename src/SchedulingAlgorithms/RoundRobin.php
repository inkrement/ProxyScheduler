<?php

namespace Inkrement\ProxyScheduler\SchedulingAlgorithms;

use Ardent\Collection\Queue;

class RoundRobin implements SchedulingInterface
{
    private $counter = 0;

    public function getNext(Queue $proxies)
    {
        if ($proxies->isEmpty()) {
            return;
        }

        $available_proxies = $proxies->count();

        //get next position
        for ($i = $this->counter;$i > 0;--$i) {
            $proxies->dequeue();
        }

        //update counter
        $this->counter = ($this->counter + 1) % $available_proxies;

        return $proxies->dequeue();
    }
}
