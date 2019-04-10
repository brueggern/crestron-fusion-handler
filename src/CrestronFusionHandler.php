<?php

namespace Brueggern\CrestronFusionHandler;

use DateTime;
use Brueggern\CrestronFusionHandler\Entities\Room;
use Brueggern\CrestronFusionHandler\Client\CrestronFusionClient;
use Brueggern\CrestronFusionHandler\Exceptions\CrestronFusionException;
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
     * @return Collection
     */
    public function getRooms() : Collection
    {
        try {
            $roomsCollection = new Collection();

            $page = 1;
            while ($page > 0) {
                $response = $this->client->getRequest('rooms', ['page' => $page]);
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

    private function transformRooms(array $response) : Collection
    {
        $collection = new Collection();

        $rooms = $response['API_Rooms'];
        foreach ($rooms['API_Room'] as $room) {
            $data = [
                'id' => $room['RoomID'],
                'name' => $room['RoomName'],
                'description' => $room['Description'],
                'lastModifiedAt' => self::transformDate($room['LastModified']),
            ];
            $collection->addItem(new Room($data));
        }

        return $collection;
    }

    /**
     * Get all appointments of a specific day
     *
     * @param DateTime $dateTime
     * @return Collection
     */
    public function getAppointments(DateTime $dateTime) : Collection
    {
        try {
        }
        catch (CrestronFusionException $e) {
        }
    }

    /**
     * Get all appointments of a specific room and day
     *
     * @param Room $room
     * @param DateTime $dateTime
     * @return Collection
     */
    public function getAppointmentsByRoom(Room $room, DateTime $dateTime) : Collection
    {
        try {
        }
        catch (CrestronFusionException $e) {
        }
    }

    private function transformAppointments(array $response) : Collection
    {
        $collection = new Collection();

        $appointments = $response['API_Appointments'];
        foreach ($appointments['API_Appointment'] as $appointment) {
            $data = [
                'id' => $appointment['AltID'],
                'subject' => $appointment['MeetingSubject'],
                'comment' => $appointment['MeetingComment'],
                'start' => self::transformDate($room['start']),
                'end' => self::transformDate($room['end']),
                'attendees' => self::transformEmployees($appointment['MeetingSubject']),
                'organizer' => self::transformEmployees($appointment['MeetingSubject']),
                'room' => self::transformRoom($appointment['MeetingSubject']),
            ];
            $collection->addItem(new Room($data));
        }

        return $collection;
    }

    /**
     * Transform Crestron Fusion date to DateTime
     *
     * @param string $cfDate
     * @return DateTime
     */
    public static function transformDate(string $cfDate) : DateTime
    {
        return new DateTime(str_replace(['T', 'Z'], [' ', ''], $cfDate));
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
     * @return Room
     */
    public static function transformRoom(string $cfRoomId) : Room
    {
        return new Room(['id' => $cfRoomId]);
    }
}
