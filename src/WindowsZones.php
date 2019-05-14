<?php

namespace Brueggern\CrestronFusionHandler;

class WindowsZones
{
    /**
     * Mapping array
     *
     * @var array
     */
    protected static $mapping = [
        'W. Europe Standard Time' => 'UTC',
        'Central European Time' => 'Europe/Zurich',
    ];

    /**
     * Return the corresponding UTC zone
     *
     * @param string $windowsZone
     * @return string
     */
    public static function getUTC(string $windowsZone)
    {
        return self::$mapping[$windowsZone] ?? null;
    }
}