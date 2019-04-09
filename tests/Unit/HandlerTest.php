<?php

namespace Tests\Unit;

use DateTime;
use Tests\BasicTest;
use Brueggern\CrestronFusionHandler\CrestronFusionHandler;

class HandlerTest extends BasicTest
{
    /**
     * @group offline
     */
    public function testParseDate()
    {
        $date = CrestronFusionHandler::convertDate('2019-03-21T11:09:56');
        $this->assertInstanceOf(DateTime::class, $date);
        $this->assertEquals($date, new DateTime('2019-03-21 11:09:56'));
    }
}