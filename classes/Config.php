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

    public function set($config)
    {
        $this->config = $config;
    }

    public function store()
    {
        $newConfig = var_export($this->config, true);
        file_put_contents(__DIR__ . '/../src/config/config.php', "<?php\n\$config = " . $newConfig . ";");
        return true;
    }
}