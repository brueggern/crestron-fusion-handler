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
                'lastModifiedAt' => self::convertDate($room['LastModified']),
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

    /**
     * Convert Crestron Fusion date to PHP DateTime
     *
     * @param string $cfDate
     * @return DateTime
     */
    public static function convertDate(string $cfDate) : DateTime
    {
        return new DateTime(str_replace(['T', 'Z'], [' ', ''], $cfDate));
    }
}
