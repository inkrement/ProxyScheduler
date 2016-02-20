<?php

namespace Inkrement\ProxyScheduler\Adapter;

use Inkrement\ProxyScheduler\Proxy;

interface AdapterInterface
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
