<?php

namespace nohn\Watermeter;

use Imagick;
use ImagickPixel;
use ImagickDraw;
use nohn\Watermeter\Debug;

class Watermeter
{
    protected $config;

    protected $sourceImage;

    protected $strokeColor;

    protected $strokeOpacity = 0.7;

    protected $sourceImageDebug;

    protected $lastValue;
    protected $lastValueTimestamp;

    public function __construct()
    {
        $config = new Config();
        $this->config = $config->get();

        $cache = new Cache();
        $this->lastValue = $cache->getValue();
        $this->lastValueTimestamp = $cache->getLastUpdate();

        $this->sourceImage = new Imagick($this->config['sourceImage']);

        $this->strokeColor = new ImagickPixel('green');

        $this->sourceImageDebug = clone $this->sourceImage;
    }

    public function writeDebugImage($path) {
        $this->sourceImageDebug->writeImage($path);
    }

    public function drawDebugImageGauge($gauge) {
        $draw = new ImagickDraw();
        $draw->setStrokeColor($this->strokeColor);
        $draw->setStrokeOpacity($this->strokeOpacity);
        $draw->setStrokeWidth(1);
        $draw->setFillOpacity(0);
        $draw->rectangle($gauge['x'], $gauge['y'], $gauge['x'] + $gauge['width'], $gauge['y'] + $gauge['height']);
        $draw->line($gauge['x'], $gauge['y'], $gauge['x'] + $gauge['width'], $gauge['y'] + $gauge['height']);
        $draw->line($gauge['x'], $gauge['y'] + $gauge['height'], $gauge['x'] + $gauge['width'], $gauge['y']);
        $this->sourceImageDebug->drawImage($draw);
    }

    public function drawDebugImageDigit($digit)
    {
        $draw = new ImagickDraw();
        $draw->setStrokeColor($this->strokeColor);
        $draw->setStrokeOpacity($this->strokeOpacity);
        $draw->setStrokeWidth(1);
        $draw->setFillOpacity(0);
        $draw->rectangle($digit['x'], $digit['y'], $digit['x'] + $digit['width'], $digit['y'] + $digit['height']);
        $this->sourceImageDebug->drawImage($draw);
    }
}