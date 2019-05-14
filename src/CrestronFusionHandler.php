<?php

namespace Brueggern\CrestronFusionHandler;

use DateTime;
use DateTimeZone;
use Brueggern\CrestronFusionHandler\WindowsZones;
use Brueggern\CrestronFusionHandler\Entities\CFRoom;
use Brueggern\CrestronFusionHandler\Entities\CFAppointment;
use Brueggern\CrestronFusionHandler\Client\CrestronFusionClient;
use Brueggern\CrestronFusionHandler\Exceptions\CrestronFusionHandlerException;

class CrestronFusionHandler
{
    /**
     * Client for the Crestron Fusion API
     *
     * @var CrestronFusionClient
     */
    protected $client;

    /**
     * Auth token and user must be specified
     *
     * @param string $baseUrl
     */
    public function __construct(string $baseUrl)
    {
        $this->client = new CrestronFusionClient($baseUrl);
    }

    /**
     * Set auth params
     *
     * @param string $authToken
     * @param string $authUser
     * @return void
     */
    public function setAuth(string $authToken, string $authUser)
    {
        $this->client->setAuth($authToken, $authUser);
    }

    /**
     * Get all rooms
     *
     * @return CFCollection
     */
    public function getRooms() : CFCollection
    {
        try {
            $roomsCollection = new CFCollection();

            $page = 1;
            while ($page > 0) {
                $response = $this->client->sendGETRequest('rooms', ['page' => $page]);
                $responseCollection = $this->transformRooms($response);
                $roomsCollection = $roomsCollection->append($responseCollection);

                $page++;
                if ($responseCollection->length() === 0) {
                    $page = 0;
                }
            }
            return $roomsCollection;
        }
        catch (CrestronFusionClientException $e) {
            throw new CrestronFusionHandlerException($e->getMessage());
        }
    }

    public function updateRoom(CFRoom $room, array $payload) : CFRoom
    {
        try {
            $response = $this->client->sendPUTRequest('rooms/'.$room->id, $payload);
            $responseCollection = $this->transformRooms($response);
            if ($responseCollection->length() === 0) {
                throw new CrestronFusionHandlerException('No room returned in response!');
            }
            return $responseCollection->get()[0];
        }
        catch (CrestronFusionClientException $e) {
            throw new CrestronFusionHandlerException($e->getMessage());
        }
    }

    /**
     * Get all appointments of a specific day
     *
     * @param DateTime $dateTime
     * @param CFCollection $rooms
     * @param int $duration
     * @return CFCollection
     */
    public function getAppointments(DateTime $dateTime, CFCollection $rooms, int $duration = 48) : CFCollection
    {
        try {
            $appointmentsCollection = new CFCollection();

            foreach ($rooms->get() as $room) {
                $params = [
                    'room' => $room->id,
                    'start' => $dateTime->format('Y-m-d'),
                    'duration' => $duration,
                ];
                $response = $this->client->sendGETRequest('appointments', $params);
                $responseCollection = $this->transformAppointments($response);
                $appointmentsCollection = $appointmentsCollection->append($responseCollection);
            }

            return $appointmentsCollection;
        }
        catch (CrestronFusionClientException $e) {
            throw new CrestronFusionHandlerException($e->getMessage());
        }
    }

    /**
     * Transform Crestron Fusion date to DateTime
     *
     * @param string $cfDate
     * @param string $timezoneId
     * @return DateTime
     */
    public static function transformDate(string $cfDate, string $timezoneId = 'Central European Time') : DateTime
    {
        $dateString = str_replace(['T', 'Z'], [' ', ''], $cfDate);
        $date = new DateTime($dateString, new DateTimeZone(WindowsZones::getUTC($timezoneId)));
        $date->setTimezone((new DateTime())->getTimezone());
        return $date;
    }

    /**
     * Transform Crestron Fusion employee string to array
     *
     * @param string $cfEmployees
     * @return array
     */
    public static function transformEmployees(string $cfEmployees) : array
    {
        preg_match_all('/[\._a-zA-Z0-9-]+@[\._a-zA-Z0-9-]+/i', html_entity_decode($cfEmployees), $matches);

        $employees = [];
        if (count($matches) > 0) {
            foreach ($matches[0] as $email) {
                $employees[] = $email;
            }
        }
        return $employees;
    }

    /**
     * Transform Crestron Fusion room id to Room entity
     *
     * @param string $cfRoomId
     * @return CFRoom
     */
    public static function transformRoom(string $cfRoomId) : CFRoom
    {
        return new CFRoom(['id' => $cfRoomId]);
    }

    /**
     * Transform response to a collection of Rooms objects
     *
     * @param array $response
     * @return CFCollection
     */
    private function transformRooms(array $response) : CFCollection
    {
        $collection = new CFCollection();

        $rooms = $response['API_Rooms'];
        if (isset($rooms['API_Room'])) {
            $aRooms = array_key_exists(0, $rooms['API_Room']) ? $rooms['API_Room'] : [$rooms['API_Room']];
            foreach ($aRooms as $room) {
                $data = [
                    'id' => $room['RoomID'],
                    'name' => $room['RoomName'],
                    'description' => $room['Description'],
                    'lastModifiedAt' => self::transformDate($room['LastModified']),
                ];
                $collection->addItem(new CFRoom($data));
            }
        }

        return $collection;
    }

    /**
     * Transform response to a collection of Appointment objects
     *
     * @param array $response
     * @return CFCollection
     */
    private function transformAppointments(array $response) : CFCollection
    {
        $collection = new CFCollection();

        $appointments = $response['API_Appointments'];
        if (!empty($appointments['API_Appointment'])) {
            $aAppointment = array_key_exists(0, $appointments['API_Appointment']) ? $appointments['API_Appointment'] : [$appointments['API_Appointment']];
            foreach ($aAppointment as $appointment) {
                $data = [
                    'id' => $appointment['RV_MeetingID'],
                    'subject' => $appointment['MeetingSubject'],
                    'comment' => $appointment['MeetingComment'],
                    'start' => self::transformDate($appointment['Start'], $appointment['TimeZoneId']),
                    'end' => self::transformDate($appointment['End'], $appointment['TimeZoneId']),
                    'attendees' => self::transformEmployees($appointment['Attendees']),
                    'organizer' => self::transformEmployees($appointment['Organizer']),
                    'room' => self::transformRoom($appointment['RoomID']),
                ];
                $collection->addItem(new CFAppointment($data));
            }
        }

        return $collection;
    }
}
