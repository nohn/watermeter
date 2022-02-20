<?php
/**
 * Watermeter
 *
 * A tool for reading water meters
 *
 * PHP version 8.1
 *
 * LICENCE: This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author    Sebastian Nohn <sebastian@nohn.net>
 * @copyright 2022 Sebastian Nohn
 * @license   http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License version 3.0
 */

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

    protected $debug;

    public function __construct($debug = false, $config = false, $lastValue = false)
    {
        if ($debug) {
            $this->debug = true;
        }
        if ($config) {
            $this->config = $config;
        } else {
            $config = new Config();
            $this->config = $config->get();
        }
        if ($lastValue) {
            $this->lastValue = $lastValue;
            $this->lastValueTimestamp = time();
        } else {
            $cache = new Cache();
            $this->lastValue = $cache->getValue();
            $this->lastValueTimestamp = $cache->getLastUpdate();
        }

        $this->sourceImage = new Imagick($this->config['sourceImage']);

        if (isset($this->config['sourceImageRotate']) && $this->config['sourceImageRotate']) {
            $sourceImageTmp = clone $this->sourceImage;
            $sourceImageTmp->rotateImage('white', $this->config['sourceImageRotate']);
            $sourceImageTmp->setImagePage($sourceImageTmp->getImageWidth(), $sourceImageTmp->getImageHeight(), 0, 0);
            $this->sourceImage = $sourceImageTmp;
        }

        if (
            isset($this->config['sourceImageCropStartX']) &&
            isset($this->config['sourceImageCropStartY']) &&
            isset($this->config['sourceImageCropSizeX']) &&
            isset($this->config['sourceImageCropSizeY']) &&
            is_numeric($this->config['sourceImageCropStartX']) &&
            is_numeric($this->config['sourceImageCropStartY']) &&
            is_numeric($this->config['sourceImageCropSizeX']) &&
            is_numeric($this->config['sourceImageCropSizeY'])
        ) {
            $sourceImageTmp = clone $this->sourceImage;
            $sourceImageTmp->cropImage(
                $this->config['sourceImageCropSizeX'],
                $this->config['sourceImageCropSizeY'],
                $this->config['sourceImageCropStartX'],
                $this->config['sourceImageCropStartY']
            );
            $sourceImageTmp->setImagePage($sourceImageTmp->getImageWidth(), $sourceImageTmp->getImageHeight(), 0, 0);
            $this->sourceImage = clone $sourceImageTmp;
        }

        if (
            (isset($this->config['sourceImageBrightness']) && $this->config['sourceImageBrightness'] !== false) ||
            (isset($this->config['sourceImageContrast']) && $this->config['sourceImageContrast'] !== false)
        ) {
            $sourceImageTmp = clone $this->sourceImage;
            $sourceImageTmp->brightnessContrastImage((float)$this->config['sourceImageBrightness'], (float)$this->config['sourceImageContrast']);
            $this->sourceImage = clone $sourceImageTmp;
        }

        if (isset($this->config['sourceImageEqualize']) && $this->config['sourceImageEqualize']) {
            $sourceImageTmp = clone $this->sourceImage;
            $sourceImageTmp->equalizeImage();
            $this->sourceImage = clone $sourceImageTmp;
        }

        $this->strokeColor = new ImagickPixel('green');
        $this->sourceImageDebug = clone $this->sourceImage;
    }

    public function writeSourceImage($path)
    {
        $this->sourceImage->writeImage($path);
    }

    public function writeDebugImage($path)
    {
        $this->sourceImageDebug->writeImage($path);
    }

    public function drawDebugImageGauge($gauge)
    {
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