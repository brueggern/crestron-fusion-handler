<?php

namespace Tests\Feature;

use DateTime;
use Tests\BasicTest;
use ReflectionMethod;
use Brueggern\CrestronFusionHandler\Collection;
use Brueggern\CrestronFusionHandler\Entities\Room;
use Brueggern\CrestronFusionHandler\Entities\Appointment;
use Brueggern\CrestronFusionHandler\CrestronFusionHandler;

class AppointmentTest extends BasicTest
{
    /**
     * @group offline
     */
    public function testTransformAppointments()
    {
        $content = file_get_contents('./tests/resources/appointmentsResponse.xml');
        $xml = simplexml_load_string($content);
        $data = json_decode(json_encode($xml), true);

        $handler = new CrestronFusionHandler('https://foobar');
        $method = new ReflectionMethod(CrestronFusionHandler::class, 'transformAppointments');
        $method->setAccessible(true);
        $collection = $method->invoke($handler, $data);

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertSame($collection->length(), 4); 
        
        foreach ($collection->get() as $appointment) {
            $this->assertInstanceOf(Appointment::class, $appointment);
            $this->assertInstanceOf(DateTime::class, $appointment->start);
            $this->assertInstanceOf(DateTime::class, $appointment->end);
            $this->assertInstanceOf(Room::class, $appointment->room);
            $this->assertTrue(is_array($appointment->attendees));
            $this->assertTrue(is_array($appointment->organizer));
        }
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

            $collection = new Collection();
            $collection->addItem(new Room(['id' => $room->id]));
            $appointmentsCollection = $handler->getAppointments(new DateTime(), $collection);

            echo($appointmentsCollection->length().'--');

            foreach ($appointmentsCollection->get() as $appointment) {
                $this->assertInstanceOf(Appointment::class, $appointment);
                $this->assertInstanceOf(DateTime::class, $room->start);
                $this->assertInstanceOf(DateTime::class, $room->end);
            }
        }
    }
}