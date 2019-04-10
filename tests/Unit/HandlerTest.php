<?php

namespace Tests\Unit;

use DateTime;
use Tests\BasicTest;
use Brueggern\CrestronFusionHandler\Entities\Room;
use Brueggern\CrestronFusionHandler\CrestronFusionHandler;

class HandlerTest extends BasicTest
{
    /**
     * @group offline
     */
    public function testTransformDate()
    {
        $dt = '2019-03-21T11:09:56';

        $date = CrestronFusionHandler::transformDate($dt);
        $this->assertInstanceOf(DateTime::class, $date);
        $this->assertEquals($date, new DateTime($dt));
    }

    /**
     * @group offline
     */
    public function testTransformEmployees()
    {
        $employees = CrestronFusionHandler::transformEmployees('"Spani, Ervin (SI BP MF ZUG MPDM LOG MH)" &lt;ervin.spani@siemens.com&gt;,"Gutzmann, Andy (SI BP MF ZUG MPDM LOG MH)" &lt;andy.gutzmann@siemens.com&gt;,"Kura, Burim (SI BP MF ZUG MPDM LOG MH)" &lt;burim.kura@siemens.com&gt;,"Dotlo, Niko (SI BP MF ZUG MPDM LOG MH)" &lt;nico.dotlo@siemens.com&gt;,"Shabani, Avdyl (SI BP MF ZUG MPDM LOG MH)" &lt;avdyl.shabani@siemens.com&gt;,"Krizevac, Nermina (SI BP MF ZUG MPDM LOG MH)" &lt;nermina.krizevac@siemens.com&gt;,"MR-CH ZUG /TH1B-2.274 (8p)" &lt;zug_th1b_274.ch@internal.siemens.com&gt;|| ');
        $this->assertTrue(is_array($employees));
        $this->assertCount(7, $employees);

        $employees = CrestronFusionHandler::transformEmployees('"Spani, Ervin (SI BP MF ZUG MPDM LOG MH)" &lt;ervin.spani@siemens.com&gt;');
        $this->assertTrue(is_array($employees));
        $this->assertCount(1, $employees);
    }

    /**
     * @group offline
     */
    public function testTransformRoom()
    {
        $roomId = 'd5089306-e95d-4024-8d54-3752c8ebd3d5';

        $room = CrestronFusionHandler::transformRoom($roomId);
        $this->assertInstanceOf(Room::class, $room);
        $this->assertEquals($room->id, $roomId);
    }
}