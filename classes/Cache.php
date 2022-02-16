<?php

namespace nohn\Watermeter;

class Cache
{
    private $value = 0;
    private $last_update = 0;

    public function __construct()
    {
        if (file_exists(__DIR__ . '/../src/config/lastValue.txt')) {
            $this->value = trim(file_get_contents(__DIR__ . '/../src/config/lastValue.txt'));
            $this->last_update = filemtime(__DIR__ . '/../src/config/lastValue.txt');
        }
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getLastUpdate()
    {
        return $this->last_update;
    }
}