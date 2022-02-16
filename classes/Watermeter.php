<?php

namespace nohn\Watermeter;

class Watermeter
{
    public function getCachedValue() {
        if (file_exists(dirname(__FILE__) .'/../src/config/lastValue.txt')) {
            $lastValue = trim(file_get_contents(dirname(__FILE__) .'/../src/config/lastValue.txt'));
        }
        else {
            $lastValue = 0;
        }
        return $lastValue;
    }
}