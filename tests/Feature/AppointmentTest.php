<?php

namespace Tests\Feature;

use DateTime;
use Tests\BasicTest;
use ReflectionMethod;
use Brueggern\CrestronFusionHandler\Collection;
use Brueggern\CrestronFusionHandler\Entities\Appointment;
use Brueggern\CrestronFusionHandler\CrestronFusionHandler;

class AppointmentTest extends BasicTest
{
    /**
     * @group offline
     */
    public function _testTransformAppointments()
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
}