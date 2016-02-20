<?php

namespace Inkrement\ProxyScheduler\Adapter;

use InvalidArgumentException;
use Ardent\Collection\LinkedQueue;
use Inkrement\ProxyScheduler\Proxy;
use Inkrement\ProxyScheduler\ProxyType;

class CSVAdapter implements AdapterInterface
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
                $cnum = count($data); //column num
              assert($cnum > 2, 'csv: not able detect mandatory columns');

                $ip = $data[0];
                $port = $data[1];
                $type = ProxyType::intFactory(intval($data[2]));

                $misses = ($cnum > 3) ? $data[3] : 0;
                $hits = ($cnum > 4) ? $data[4] : 0;
                $last_used = ($cnum > 5) ? $data[5] : 0;
                $rating = ($cnum > 6) ? $data[6] : 0;

                $this->proxies->enqueue(new Proxy($ip, $port, $type, $misses, $hits, $last_used, $rating));
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
