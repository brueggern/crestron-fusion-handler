<?php

namespace Brueggern\CrestronFusionHandler;

use DateTime;
use Brueggern\CrestronFusionHandler\Collection;
use Brueggern\CrestronFusionHandler\Entities\Room;
use Brueggern\CrestronFusionHandler\Exceptions\CrestronFusionException;

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
     * @param string $authToken
     * @param string $authUser
     */
    public function __construct(string $authToken, string $authUser)
    {
        $this->client = new CrestronFusionClient($authToken, $authUser);
    }

    /**
     * Get all rooms
     *
     * @return Collection
     */
    public function getRooms() : Collection
    {
        try {
            
        }
        catch (CrestronFusionException $e) {

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
