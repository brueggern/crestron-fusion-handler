<?php

namespace Brueggern\CrestronFusionHandler\Entities;

use DateTime;
use Brueggern\CrestronFusionHandler\Exceptions\CrestronFusionException;

class CFAppointment
{
    /**
     * uuid
     *
     * @var string
     */
    public $id = null;

    /**
     * subject
     *
     * @var string
     */
    public $subject = null;

    /**
     * comment
     *
     * @var string
     */
    public $comment = null;

    /**
     * start time
     *
     * @var DateTime
     */
    private $start = null;

    /**
     * end time
     *
     * @var DateTime
     */
    private $end = null;

    /**
     * attendees
     *
     * @var array
     */
    private $attendees = null;

    /**
     * organizer
     *
     * @var array
     */
    private $organizer = null;

    /**
     * room
     *
     * @var CFRoom
     */
    private $room = null;

    /**
     * Create a new apponintment entity
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        if (isset($data['start']) && !$data['start'] instanceof DateTime) {
            throw new CrestronFusionException('Invalid type for start');
        }
        if (isset($data['end']) && !$data['end'] instanceof DateTime) {
            throw new CrestronFusionException('Invalid type for end');
        }
        if (isset($data['attendees']) && !is_array($data['attendees'])) {
            throw new CrestronFusionException('Invalid type for attendees');
        }
        if (isset($data['organizer']) && !is_array($data['organizer'])) {
            throw new CrestronFusionException('Invalid type for organizer');
        }
        if (isset($data['room']) && !$data['room'] instanceof CFRoom) {
            throw new CrestronFusionException('Invalid type for room');
        }

        $this->id = $data['id'] ?? null;
        $this->subject = $data['subject'] ?? null;
        $this->start = $data['start'] ?? null;
        $this->end = $data['end'] ?? null;
        $this->attendees = $data['attendees'] ?? null;
        $this->organizer = $data['organizer'] ?? null;
        $this->room = $data['room'] ?? null;
        $this->comment = $data['comment'] ?? null;
    }

    /**
     * Get a property value
     *
     * @param string $name
     * @return mix
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
        if ($name === 'start' || $name === 'end') {
            if ($value instanceof DateTime) {
                $this->{$name} = $value;
                return;
            }
            throw new CrestronFusionException('Invalid type for '.$name);
        }

        if ($name === 'attendees' || $name === 'organizer') {
            if (is_array($value)) {
                $this->{$name} = $value;
                return;
            }
            throw new CrestronFusionException('Invalid type for '.$name);
        }

        if ($name === 'room') {
            if ($value instanceof CFRoom) {
                $this->{$name} = $value;
                return;
            }
            throw new CrestronFusionException('Invalid type for '.$name);
        }
    }
}
