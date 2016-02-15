<?php

namespace Inkrement\ProxyScheduler\DataAbstraction;

use InvalidArgumentException;
use Ardent\Collection\LinkedQueue;
use Inkrement\ProxyScheduler\Proxy;
use Inkrement\ProxyScheduler\ProxyType;

class CSVAdapter implements StorageInterface
{
    private $file_path;
    private $delimiter;
    private $proxies;

    public function __construct($file_path, $delimiter = ';')
    {
        if (is_null($file_path) || !is_string($file_path)) {
            throw new InvalidArgumentException('no file provided!');
        }
        $this->file_path = $file_path;
        $this->delimiter = $delimiter;
        $this->loadCSV($file_path);
    }

    public function __destruct()
    {
        $this->saveCSV();
    }

    private function saveCSV()
    {
        $fp = fopen($this->file_path, 'w');
        $iterator = $this->proxies->getIterator();

        while ($iterator->valid()) {
            $p = $iterator->current();
            fputcsv($fp, [$p->getIP(), $p->getPort(), $p->getType(), $p->getMisses(), $p->getHits(), $p->getLastUsed(), $p->getRating()], $this->delimiter);

            $iterator->next();
        }

        fclose($fp);
    }

    public function updateLastUsed(Proxy $proxy, $timestamp)
    {
        foreach ($this->proxies->getIterator() as $p) {
            if ($proxy == $p) {
                $p->setLastUsed($timestamp);

                $this->saveCSV();

                return true;
            }
        }

        assert(false, 'the provided proxy does not exist. not able to update!');
    }

    private function loadCSV($file_path)
    {
        $this->proxies = new LinkedQueue();

        if (($handle = fopen($file_path, 'r')) !== false) {
            while (($data = fgetcsv($handle, 1000, $this->delimiter)) !== false) {
                $this->proxies->enqueue(
                  new Proxy(
                    $data[0], //IP
                    $data[1], //Port
                    ProxyType::factory(intval($data[2])), // type
                    $data[3], //misses
                    $data[4], //hits
                    $data[5], //last used
                    $data[6] //rating
                  ));
            }
            fclose($handle);
        }
    }

    public function count()
    {
        return $this->proxies->count();
    }

    /**
     * getProxies.
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

    public function getFreshProxies($timetowait = false)
    {
        $proxies = new LinkedQueue();
        $now = time();

        $iterator = $this->proxies->getIterator();

        while ($iterator->valid()) {
            $proxy = $iterator->current();

            if (false === $timetowait || $proxy->getLastUsed() < $now - $timetowait) {
                $proxies->enqueue($proxy);
            }

            $iterator->next();
        }

        return $proxies;
    }
}
