<?php

namespace Inkrement\ProxyScheduler\Test;

use Inkrement\ProxyScheduler\Adapter\CSVAdapter;
use Inkrement\ProxyScheduler\ProxyScheduler;

class ProxyTest extends \PHPUnit_Framework_TestCase
{
    public function testCSVProxy()
    {
        $dao = new CSVAdapter(__DIR__.'/test_multipleentries.csv');
        $scheduler = new ProxyScheduler($dao);
        $proxy = $scheduler->getNext();

        //$this->assertEquals($proxy->getID(), 'http://207.91.10.234:8080');
    }
}
