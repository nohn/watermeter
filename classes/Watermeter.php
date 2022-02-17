<?php

namespace nohn\Watermeter;

use Imagick;
use ImagickPixel;

class Watermeter
{
    protected $config;

    protected $sourceImage;

    protected $strokeColor;

    protected $strokeOpacity = 0.7;

    protected $sourceImageDebug;

    public function __construct()
    {
        $config = new Config();
        $this->config = $config->get();

        $this->sourceImage = new Imagick($this->config['sourceImage']);

        $this->strokeColor = new ImagickPixel('white');

        $this->sourceImageDebug = clone $this->sourceImage;
    }
}