<?php

namespace Tests\Feature;

use DateTime;
use Tests\BasicTest;
use ReflectionMethod;
use Brueggern\CrestronFusionHandler\CFCollection;
use Brueggern\CrestronFusionHandler\Entities\CFRoom;
use Brueggern\CrestronFusionHandler\Entities\CFAppointment;
use Brueggern\CrestronFusionHandler\CrestronFusionHandler;

class AppointmentTest extends BasicTest
{
    /**
     * @group offline
     */
    public function testTransformAppointments()
    {
        $handler = new CrestronFusionHandler('https://foobar');

        $content = file_get_contents('./tests/resources/appointmentsResponse1.xml');
        $xml = simplexml_load_string($content);
        $data = json_decode(json_encode($xml), true);

        $method = new ReflectionMethod(CrestronFusionHandler::class, 'transformAppointments');
        $method->setAccessible(true);
        $collection = $method->invoke($handler, $data);

        $this->assertInstanceOf(CFCollection::class, $collection);
        $this->assertSame($collection->length(), 4);

        foreach ($collection->get() as $appointment) {
            $this->assertInstanceOf(CFAppointment::class, $appointment);
            $this->assertInstanceOf(DateTime::class, $appointment->start);
            $this->assertInstanceOf(DateTime::class, $appointment->end);
            $this->assertInstanceOf(CFRoom::class, $appointment->room);
            $this->assertTrue(is_array($appointment->attendees));
            $this->assertTrue(is_array($appointment->organizer));
        }

        $content = file_get_contents('./tests/resources/appointmentsResponse2.xml');
        $xml = simplexml_load_string($content);
        $data = json_decode(json_encode($xml), true);

        $method = new ReflectionMethod(CrestronFusionHandler::class, 'transformAppointments');
        $method->setAccessible(true);
        $collection = $method->invoke($handler, $data);

        $this->assertInstanceOf(CFCollection::class, $collection);
        $this->assertSame($collection->length(), 1);
    }

    /**
     * @group online
     */
    public function testFetchAppointments()
    {
        $handler = new CrestronFusionHandler(getenv('API_URL'));
        $handler->setAuth(getenv('AUTH_TOKEN'), getenv('AUTH_USER'));

        $roomsCollection = $handler->getRooms();

        if ($roomsCollection->length() > 0) {
            $room = $roomsCollection->getItem(0);

            $collection = new CFCollection();
            $collection->addItem(new CFRoom(['id' => $room->id]));
            $appointmentsCollection = $handler->getAppointments(new DateTime(), $collection);

            $this->assertInstanceOf(CFCollection::class, $appointmentsCollection);

            foreach ($appointmentsCollection->get() as $appointment) {
                $this->assertInstanceOf(CFAppointment::class, $appointment);
                $this->assertInstanceOf(DateTime::class, $room->start);
                $this->assertInstanceOf(DateTime::class, $room->end);
            }
        }
        else {
            $this->assertSame($roomsCollection->length(), 0);
        }
    }
}
