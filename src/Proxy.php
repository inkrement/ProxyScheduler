<?php

namespace Inkrement\ProxyScheduler;

/**
 * Proxy Item.
 */
class Proxy
{
    private $uri;
    private $type;
    private $misses;
    private $hits;
    private $last_used;

    /*
     * -1 on miss
     * 0 if unset
     * 1/delay on hit
     */
    private $rating;

    public function __construct($ipport, $type, $misses = 0, $hits = 0, $last_used = 0, $rating = 0)
    {
        $this->ipport = $ipport;
        $this->type = $type;
        $this->misses = intval($misses);
        $this->hits = intval($hits);
        $this->last_used = intval($last_used);
        $this->rating = doubleval($rating);
    }

    public function getID()
    {
        return ProxyType::toString($this->type).$this->ipport;
    }

    public function getIPPort()
    {
        return $this->ipport;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getMisses()
    {
        return $this->misses;
    }

    public function getHits()
    {
        return $this->hits;
    }

    public function getLastUsed()
    {
        return $this->last_used;
    }

    public function getRating()
    {
        return $this->rating;
    }

    public function addMiss()
    {
        ++$this->misses;
        $this->rating = -1;
        $this->last_used = time();
    }

    public function addHit($delay = null)
    {
        ++$this->hit;
        $this->rating = (is_null($delay)) ? 1 : 1 / intval($delay);
        $this->last_used = time();
    }

    public function setLastUsed($timestamp)
    {
        $this->last_used = intval($timestamp);
    }
}
