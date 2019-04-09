<?php

namespace Tests\Unit;

use DateTime;
use DateInterval;
use Tests\BasicTest;
use Brueggern\CrestronFusionHandler\Entities\Room;
use Brueggern\CrestronFusionHandler\Exceptions\CrestronFusionException;

class RoomTest extends BasicTest
{
    /**
     * @group offline
     */
    public function testCreateRoom()
    {
        $dateTime = new DateTime();

        $data = [
            'id' => 'this is a string.',
            'name' => 'this is a string.',
            'description' => 'this is a string.',
            'lastModifiedAt' => $dateTime,
        ];
        $room1 = new Room($data);
        $this->assertSame($room1->id, $data['id']);
        $this->assertSame($room1->name, $data['name']);
        $this->assertSame($room1->description, $data['description']);
        $this->assertSame($room1->lastModifiedAt, $data['lastModifiedAt']);

        $data = [
            'id' => 'this is a string.',
            'name' => 'this is a string.',
            'description' => 'this is a string.',
            'lastModifiedAt' => $dateTime->format('Y-m-d H:i:s'),
        ];
        $this->expectException(CrestronFusionException::class);
        $room1 = new Room($data);
    }

    /**
     * @group offline
     */
    public function testUpdateRoom()
    {
        $dateTime1 = new DateTime();

        $data = [
            'id' => 'this is a string.',
            'name' => 'this is a string.',
            'description' => 'this is a string.',
            'lastModifiedAt' => $dateTime1,
        ];
        $room1 = new Room($data);

        $dateTime2 = new DateTime();
        $dateTime2->sub(new DateInterval('P1D'));
        $room1->lastModifiedAt = $dateTime2;

        $this->assertNotSame($room1->lastModifiedAt, $dateTime1);

        $this->expectException(CrestronFusionException::class);
        $room1->lastModifiedAt = $dateTime2->format('Y-m-d H:i:s');
    }
}
