<?php

namespace Inkrement\ProxyScheduler\Test;

use Inkrement\ProxyScheduler\Adapter\GimmeProxyAdapter;

class GimmeProxyAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group new
     */
    public function testNonEmptyReturn()
    {
        $dao = new GimmeProxyAdapter(0);
        $list = $dao->getProxies();

        $this->assertNotNull($list);

        $this->assertCount(1, $list);
    }

    /**
     * @group new
     */
    public function testFetchNewProxy()
    {
        $dao = new GimmeProxyAdapter(0);
        //load fist ip
        $dao->getProxies();

        sleep(1);
        //force a new load
        $list = $dao->getFreshProxies(0);

        $this->assertCount(2, $list);

        sleep(1);
        //force a new load
        $list = $dao->getFreshProxies(0);

        $this->assertCount(3, $list);
    }
}
