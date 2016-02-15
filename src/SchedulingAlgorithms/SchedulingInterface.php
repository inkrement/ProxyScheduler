<?php

namespace Inkrement\ProxyScheduler\SchedulingAlgorithms;

use Ardent\Collection\Queue;

interface SchedulingInterface
{
    public function getNext(Queue $proxies);
}
