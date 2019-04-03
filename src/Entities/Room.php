<?php

namespace Brueggern\CrestronFusionHandler\Entities;

use DateTime;
use Brueggern\CrestronFusionHandler\Exceptions\CrestronFusionException;

class Room 
{
    /**
     * uuid
     *
     * @var string
     */
    public $id = null;

    /**
     * Name of the room
     *
     * @var string
     */
    public $name = null;

    /**
     * Last modified time
     *
     * @var DateTime
     */
    public $lastModifiedAt = null;

    /**
     * Description text
     *
     * @var string
     */
    public $description = null;

    /**
     * Create a new room entity
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? null;
        $this->lastModifiedAt = isset($data['lastModifiedAt']) ? new DateTime($data['lastModifiedAt']) : null;
        $this->description = $data['description'] ?? null;
    }

    /**
     * Set a property value
     *
     * @param string $name
     * @param any $value
     */
    public function __set(string $name, any $value)
    {
        switch ($name) {

            case 'lastModifiedAt':
                if ($value instanceof DateTime) {
                    $this->${$name} = $value;
                }
                else {
                    throw new CrestronFusionException('Invalid type for lastModifiedAt');
                }
                break;

            default:
                $this->${$name} = $value;
                break;
        }
    }
}