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

                $rooms = $response['API_Rooms'];
                foreach ($rooms as $room) {
                    $lastModified = str_replace(['/Date(', ')/'], '', $room['LastModified']);
                    $timestamp = (explode('+', $lastModified)[0] / 1000);

                    $data = [
                        'id' => $room['RoomID'],
                        'name' => $room['RoomName'],
                        'description' => $room['Description'],
                        'lastModifiedAt' => (new DateTime())->setTimestamp($timestamp),
                    ];
                    $roomsCollection->addItem(new Room($data));
                }

                $page++;
                if (count($rooms) === 0) {
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
}
