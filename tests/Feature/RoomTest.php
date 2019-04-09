<?php

namespace Tests\Feature;

use DateTime;
use Tests\BasicTest;
use Brueggern\CrestronFusionHandler\Entities\Room;
use Brueggern\CrestronFusionHandler\CrestronFusionHandler;

class RoomTest extends BasicTest
{
    /**
     * @group offline
     */
    public function testTransformRooms()
    {
        $this->assertTrue(true);
    }

    /**
     * @group online
     */
    public function testFetchRooms()
    {
        $handler = new CrestronFusionHandler(getenv('API_URL'));
        $handler->setAuth(getenv('AUTH_TOKEN'), getenv('AUTH_USER'));
        $roomsCollection = $handler->getRooms();

        foreach ($roomsCollection->get() as $room) {
            $this->assertInstanceOf(Room::class, $room);
            $this->assertInstanceOf(DateTime::class, $room->lastModifiedAt);
        }
    }
}