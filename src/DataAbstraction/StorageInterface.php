<?php

namespace Inkrement\ProxyScheduler\DataAbstraction;

use Inkrement\ProxyScheduler\Proxy;

interface StorageInterface
{
    /**
     * getProxies.
     *
     * @return Ardent\Collection\Collection collection
     */
    public function getProxies();

    public function getFreshProxies($timetowait);

    public function updateLastUsed(Proxy $proxy, $timestamp);

    public function count();
}
