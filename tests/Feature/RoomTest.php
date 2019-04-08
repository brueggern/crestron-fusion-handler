<?php

namespace Tests\Feature;

use Tests\BasicTest;

class RoomTest extends BasicTest
{
    public function fetchRooms()
    {
        $client = new CrestronFusionClient(getenv('AUTH_TOKEN'), getenv('AUTH_USER'));
        
    }
}