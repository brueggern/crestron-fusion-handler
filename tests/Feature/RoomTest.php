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
        $handler = new CrestronFusionHandler('https://foobar');

        $content = file_get_contents('./tests/resources/roomsResponse1.xml');
        $xml = simplexml_load_string($content);
        $data = json_decode(json_encode($xml), true);

        $method = new ReflectionMethod(CrestronFusionHandler::class, 'transformRooms');
        $method->setAccessible(true);
        $collection = $method->invoke($handler, $data);

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertSame($collection->length(), 25);

        foreach ($collection->get() as $room) {
            $this->assertInstanceOf(Room::class, $room);
            $this->assertInstanceOf(DateTime::class, $room->lastModifiedAt);
        }

        $content = file_get_contents('./tests/resources/roomsResponse2.xml');
        $xml = simplexml_load_string($content);
        $data = json_decode(json_encode($xml), true);

        $method = new ReflectionMethod(CrestronFusionHandler::class, 'transformRooms');
        $method->setAccessible(true);
        $collection = $method->invoke($handler, $data);

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertSame($collection->length(), 1);
    }

    /**
     * @group online
     */
    public function testGetRooms()
    {
        $handler = new CrestronFusionHandler(getenv('API_URL'));
        $handler->setAuth(getenv('AUTH_TOKEN'), getenv('AUTH_USER'));
        $roomsCollection = $handler->getRooms();

        foreach ($roomsCollection->get() as $room) {
            $this->assertInstanceOf(Room::class, $room);
            $this->assertInstanceOf(DateTime::class, $room->lastModifiedAt);
        }
    }

    /**
     * @group online
     */
    public function _testUpdateRoom()
    {
        $handler = new CrestronFusionHandler(getenv('API_URL'));
        $handler->setAuth(getenv('AUTH_TOKEN'), getenv('AUTH_USER'));
        $roomsCollection = $handler->getRooms();

        if ($roomsCollection->length() > 0) {
            $roomOriginal = $roomsCollection->get()[0];
            $room = $handler->updateRoom($roomOriginal, ['GroupwarePassword' => 'test1234']);
            $this->assertNotSame($roomOriginal->groupPwareassword, $room->groupwarePassword);
        }
    }
}
