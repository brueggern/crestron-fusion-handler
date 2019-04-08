<?php

namespace Tests\Unit;

use DateTime;
use Tests\BasicTest;
use Brueggern\CrestronFusionHandler\Collection;
use Brueggern\CrestronFusionHandler\Entities\Room;
use Brueggern\CrestronFusionHandler\Exceptions\CollectionException;

class CollectionTest extends BasicTest
{
    public function testAddItem()
    {
        $collection = $this->createCollection();
        $collection->addItem('Foo', 'foo');

        $this->expectException(CollectionException::class);
        $collection->addItem('Foo', 'foo');
    }

    public function testGetItem()
    {
        $collection = $this->createCollection();
        $collection->addItem('Foo');

        $this->assertSame('Room 1', $collection->getItem('room1')->name);
        $this->assertSame('Room 2', $collection->getItem(1)->name);
        $this->assertSame('Foo', $collection->getItem(2));
        $this->expectException(CollectionException::class);
        $collection->getItem('bar');
    }

    public function testDeleteItemString()
    {
        $collection = $this->createCollection();
        $this->assertSame('Room 2', $collection->getItem(1)->name);
        $collection->deleteItem('room2');

        $this->expectException(CollectionException::class);
        $collection->getItem('room2');
    }

    public function testDeleteItemInteger()
    {
        $collection = $this->createCollection();
        $this->assertSame('Room 2', $collection->getItem(1)->name);
        $collection->deleteItem(1);

        $this->expectException(CollectionException::class);
        $collection->getItem(1);
    }

    public function testIteration()
    {
        $collection = $this->createCollection();
        foreach ($collection->get() as $item) {
            $this->assertIsString($item->name);
        }
    }

    private function createCollection()
    {
        $dateTime = new DateTime();

        $data = [
            'id' => '1',
            'name' => 'Room 1',
            'description' => 'this is a string.',
            'lastModifiedAt' => $dateTime,
        ];
        $room1 = new Room($data);

        $data = [
            'id' => '2',
            'name' => 'Room 2',
            'description' => 'this is a string.',
            'lastModifiedAt' => $dateTime,
        ];
        $room2 = new Room($data);

        $collection = new Collection();
        $collection->addItem($room1, 'room1');
        $collection->addItem($room2, 'room2');

        return $collection;
    }
}
