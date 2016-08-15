<?php

namespace Inkrement\ProxyScheduler\Adapter;

use Ardent\Collection\LinkedQueue;
use Inkrement\ProxyScheduler\Proxy;
use Inkrement\ProxyScheduler\ProxyType;

/**
 * Gimme Proxy API Adapter.
 *
 * @todo:
 *  - prune list from time to time
 *  - misses/hits feedback
 */
class GimmeProxyAdapter implements AdapterInterface
{
    private $proxies;
    private $last_proxy;
    private $max_reload_rate;

    public function __construct($max_reload_rate = 30)
    {
        $this->max_reload_rate = $max_reload_rate;
        $this->loadFreshProxy();
    }

    /**
     * load fresh proxy from gimmy proxy api.
     *
     * @todo: error handling
     */
    private function loadFreshProxy()
    {
        if (is_null($this->proxies)) {
            $this->proxies = new LinkedQueue();
        }

        if ($this->last_proxy + $this->max_reload_rate > time()) {
            //do not load new one because reload limit is violated
          return;
        }

        $this->last_proxy = time();

        $raw_return = file_get_contents('http://gimmeproxy.com/api/getProxy');
        $json = json_decode($raw_return, true);

        $this->proxies->enqueue(new Proxy($json['ip'], $json['port'], ProxyType::stringFactory($json['type']), 0, 0, 0, 0));
    }

    /**
     * returns copy of proxy list.
     */
    public function getProxies()
    {
        $proxies = new LinkedQueue();

        $iterator = $this->proxies->getIterator();

        while ($iterator->valid()) {
            $proxies->enqueue($iterator->current());
            $iterator->next();
        }

        return $proxies;
    }

    /**
     * reloads proxy list.
     *
     * "fresh" has two meanings in this context
     * - proxies not used withing last $max_age seconds
     * - one new proxy which will be loaded from the api (if reload limit is not violated)
     */
    public function getFreshProxies($max_age = 20)
    {
        $now = time();

        //@todo: change false and null. same for CSVAdapter
        if ($max_age === false) {
            $max_age = $now;
        }

        // try to load new one
        $this->loadFreshProxy();

        $proxies = new LinkedQueue();
        $iterator = $this->proxies->getIterator();

        while ($iterator->valid()) {
            $proxy = $iterator->current();

            if (false === $max_age || $proxy->getLastUsed() < $now - $max_age) {
                $proxies->enqueue($proxy);
            }

            $iterator->next();
        }

        return $proxies;
    }

    public function updateLastUsed(Proxy $proxy, $timestamp)
    {
        foreach ($this->proxies->getIterator() as $p) {
            if ($proxy == $p) {
                $p->setLastUsed($timestamp);

                return true;
            }
        }
    }

    public function count()
    {
        return $this->proxies->count();
    }
}
