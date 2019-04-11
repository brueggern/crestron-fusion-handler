<?php

namespace Brueggern\CrestronFusionHandler;

use DateTime;
use Brueggern\CrestronFusionHandler\Entities\Room;
use Brueggern\CrestronFusionHandler\Entities\Appointment;
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
     * @return Collection
     */
    public function getRooms() : Collection
    {
        try {
            $roomsCollection = new Collection();

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

    /**
     * Get all appointments of a specific day
     *
     * @param DateTime $dateTime
     * @return Collection
     */
    public function getAppointments(DateTime $dateTime, Collection $rooms) : Collection
    {
        try {
            $appointmentsCollection = new Collection();

            foreach ($rooms->get() as $room) {
                $params = [
                    'room' => $room->id,
                    'start' => $dateTime->format('Y-m-d'),
                    'duration' => 24,
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

    /**
     * Transform response to a collection of Rooms objects
     *
     * @param array $response
     * @return Collection
     */
    private function transformRooms(array $response) : Collection
    {
        $collection = new Collection();

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
                $collection->addItem(new Room($data));
            }
        }

        return $collection;
    }

    /**
     * Transform response to a collection of Appointment objects
     *
     * @param array $response
     * @return Collection
     */
    private function transformAppointments(array $response) : Collection
    {
        $collection = new Collection();

        $appointments = $response['API_Appointments'];
        if (!empty($appointments['API_Appointment'])) {
            $aAppointment = array_key_exists(0, $appointments['API_Appointment']) ? $appointments['API_Appointment'] : [$appointments['API_Appointment']];
            foreach ($aAppointment as $appointment) {
                $data = [
                    'id' => $appointment['RV_MeetingID'],
                    'subject' => $appointment['MeetingSubject'],
                    'comment' => $appointment['MeetingComment'],
                    'start' => self::transformDate($appointment['Start']),
                    'end' => self::transformDate($appointment['End']),
                    'attendees' => self::transformEmployees($appointment['Attendees']),
                    'organizer' => self::transformEmployees($appointment['Organizer']),
                    'room' => self::transformRoom($appointment['RoomID']),
                ];
                $collection->addItem(new Appointment($data));
            }
        }

        return $collection;
    }
}
