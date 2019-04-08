<?php

namespace Tests\Unit;

use Tests\BasicTest;
use Brueggern\CrestronFusionHandler\Client\CrestronFusionClient;
use Brueggern\CrestronFusionHandler\Exceptions\CrestronFusionClientException;

class ClientTest extends BasicTest
{
    public function testConnectionExcpetion()
    {
        $client = new CrestronFusionClient();
        $this->expectException(CrestronFusionClientException::class);
        $client->getRequest('https://foobar.ch', []);
    }
}
