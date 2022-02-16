<?php

namespace nohn\Watermeter;

class Cache
{
    private $value = 0;
    private $last_update = 0;

    public function __construct()
    {
        $cacheFile = __DIR__ . '/../src/config/lastValue.txt';
        if (file_exists($cacheFile)) {
            $this->value = trim(file_get_contents($cacheFile));
            $this->last_update = filemtime($cacheFile);
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