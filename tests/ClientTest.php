<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use Brueggern\CrestronFusionHandler\Client\CrestronFusionClient;
use Brueggern\CrestronFusionHandler\Client\CrestronFusionClientException;

class BasicTest extends TestCase
{
    public function testConnectionExcpetion()
    {
        $client = new CrestronFusionClient('foo', 'bar');
        
        $this->expectException(CrestronFusionClientException::class);
        $client->getRequest('https://foobar.ch', []);
    }
}