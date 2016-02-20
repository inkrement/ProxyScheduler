<?php

namespace Inkrement\ProxyScheduler\Test;

use Inkrement\ProxyScheduler\Adapter\CSVAdapter;

class AdapterTest extends \PHPUnit_Framework_TestCase
{
    public function testEmptyCSV()
    {
        $empty_file = tempnam(sys_get_temp_dir(), 'scheduler_test');
        $dao = new CSVAdapter($empty_file);
        $proxies = $dao->getProxies();

        $this->assertInstanceOf('Ardent\Collection\Queue', $proxies);
        $this->assertCount(0, $proxies);
    }

    /**
     *
     */
    public function testProxyListOrder()
    {
        $dao = new CSVAdapter(__DIR__.'/test_multipleentries.csv');
        $list = $dao->getProxies();

        $this->assertInstanceOf('Ardent\Collection\Queue', $list);
        $this->assertCount(5, $list);

        $this->assertEquals('http://201.91.10.234:8080', $list->dequeue()->getID());
        $this->assertEquals('http://202.91.10.234:8080', $list->dequeue()->getID());
        $this->assertEquals('http://203.91.10.234:8080', $list->dequeue()->getID());
        $this->assertEquals('http://204.91.10.234:8080', $list->dequeue()->getID());
        $this->assertEquals('http://205.91.10.234:8080', $list->dequeue()->getID());
    }

    /**
     * should only return 3 not 5.
     */
    public function testFreshProxyListOrder()
    {
        $dao = new CSVAdapter(__DIR__.'/test_multipleentries.csv');

        $list = $dao->getFreshProxies(false);

        $this->assertInstanceOf('Ardent\Collection\Queue', $list);
        $this->assertEquals(5, $list->count());

        $ids = ['http://201.91.10.234:8080', 'http://202.91.10.234:8080',
        'http://203.91.10.234:8080', 'http://204.91.10.234:8080', 'http://205.91.10.234:8080', ];

        //without touching last used
        foreach ($ids as $id) {
            $p = $list->dequeue();
            $this->assertEquals($id, $p->getID());
            $dao->updateLastUsed($p, time());
        }

        $blockingtime = 3;
        $list = $dao->getFreshProxies($blockingtime);

        $this->assertCount(0, $list);
        sleep($blockingtime + 2);

        $list = $dao->getFreshProxies($blockingtime);

        $this->assertCount(5, $list);
    }
}
