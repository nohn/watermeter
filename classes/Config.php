<?php

namespace nohn\Watermeter;

class Config
{
    private $config = array();

    public function __construct()
    {
        require __DIR__ . '/../src/config/config.php';
        $this->config = $config;
    }

    public function get()
    {
        return $this->config;
    }

}