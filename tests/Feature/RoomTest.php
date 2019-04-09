<?php

namespace Tests\Feature;

use DateTime;
use Tests\BasicTest;
use ReflectionMethod;
use Brueggern\CrestronFusionHandler\Collection;
use Brueggern\CrestronFusionHandler\Entities\Room;
use Brueggern\CrestronFusionHandler\CrestronFusionHandler;

class RoomTest extends BasicTest
{
    /**
     * @group offline
     */
    public function testTransformRooms()
    {
        $content = file_get_contents('./tests/resources/roomsResponse.xml');
        $xml = simplexml_load_string($content);
        $data = json_decode(json_encode($xml), true);

        $handler = new CrestronFusionHandler('https://foobar');
        $method = new ReflectionMethod(CrestronFusionHandler::class, 'transformRooms');
        $method->setAccessible(true);
        $collection = $method->invoke($handler, $data);

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertSame($collection->length(), 25);
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