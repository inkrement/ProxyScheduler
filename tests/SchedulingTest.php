<?php

namespace Inkrement\ProxyScheduler\Test;

use Inkrement\ProxyScheduler\DataAbstraction\CSVAdapter;
use Inkrement\ProxyScheduler\ProxyScheduler;
use Inkrement\ProxyScheduler\SchedulingAlgorithms\Random as RandomScheduling;
use Inkrement\ProxyScheduler\SchedulingAlgorithms\RoundRobin as RoundRobinScheduling;

class SchedulingTest extends \PHPUnit_Framework_TestCase
{
    public function testRandomScheduling()
    {
        $dao = new CSVAdapter(__DIR__.'/test_oneentry.csv');
        $this->assertEquals($dao->count(), 1);
        $scheduler = new ProxyScheduler($dao, new RandomScheduling());

        $proxy = $scheduler->getNext();

        $this->assertInstanceOf('\Inkrement\ProxyScheduler\Proxy', $proxy);

        $this->assertEquals($proxy->getID(), 'http://207.91.10.234:8080');
    }

    /**
     * @group failing
     */
    public function testRoundRobinScheduling()
    {
        $dao = new CSVAdapter(__DIR__.'/test_multipleentries.csv');
        $this->assertEquals($dao->count(), 5);

        $scheduler = new ProxyScheduler($dao, new RoundRobinScheduling(), false);

        $this->assertEquals('http://201.91.10.234:8080', $scheduler->getNext()->getID());
        $this->assertEquals('http://202.91.10.234:8080', $scheduler->getNext()->getID());
        $this->assertEquals('http://203.91.10.234:8080', $scheduler->getNext()->getID());
        $this->assertEquals('http://204.91.10.234:8080', $scheduler->getNext()->getID());
        $this->assertEquals('http://205.91.10.234:8080', $scheduler->getNext()->getID());
        $this->assertEquals('http://201.91.10.234:8080', $scheduler->getNext()->getID());
    }
}
