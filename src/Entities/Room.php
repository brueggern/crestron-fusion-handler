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
     * Description text
     *
     * @var string
     */
    public $description = null;

    /**
     * Groupware password
     *
     * @var string
     */
    public $groupwarePassword = null;

    /**
     * Last modified time
     *
     * @var DateTime
     */
    private $lastModifiedAt = null;

    /**
     * Create a new room entity
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        if (isset($data['lastModifiedAt']) && !$data['lastModifiedAt'] instanceof DateTime) {
            throw new CrestronFusionException('Invalid type for lastModifiedAt');
        }

        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? null;
        $this->description = $data['description'] ?? null;
        $this->groupwarePassword = $data['groupwarePassword'] ?? null;
        $this->lastModifiedAt = $data['lastModifiedAt'] ?? null;
    }

    /**
     * Get a property value
     *
     * @param string $name
     * @return void
     */
    public function __get(string $name)
    {
        return $this->{$name};
    }

    /**
     * Set a property value
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set(string $name, $value)
    {
        if ($name === 'lastModifiedAt') {
            if ($value instanceof DateTime) {
                $this->lastModifiedAt = $value;
            }
            else {
                throw new CrestronFusionException('Invalid type for lastModifiedAt');
            }
        }
    }
}
