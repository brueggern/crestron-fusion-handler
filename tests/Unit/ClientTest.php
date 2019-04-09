<?php

namespace Tests\Unit;

use Tests\BasicTest;
use Brueggern\CrestronFusionHandler\Client\CrestronFusionClient;
use Brueggern\CrestronFusionHandler\Exceptions\CrestronFusionClientException;

class ClientTest extends BasicTest
{
    /**
     * @group offline
     */
    public function testConnectionExcpetion()
    {
        $client = new CrestronFusionClient('https://foobar.ch');
        $this->expectException(CrestronFusionClientException::class);
        $client->getRequest('', []);
    }
}
