<?php

namespace Inkrement\ProxyScheduler\SchedulingAlgorithms;

use Ardent\Collection\Queue;

class Random implements SchedulingInterface
{
    public function getNext(Queue $proxies)
    {
        if ($proxies->isEmpty()) {
            return;
        }

        $index = rand(0, $proxies->count()  - 1);

        for ($i = $index; $i > 0; --$i) {
            $proxies->dequeue();
        }

        return $proxies->dequeue();
    }
}
