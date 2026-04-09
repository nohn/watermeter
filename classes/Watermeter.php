<?php
/**
 * Watermeter
 *
 * A tool for reading water meters
 *
 * PHP Version 8.3
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
 * @copyright 2026 Sebastian Nohn
 * @license   http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License version 3.0
 */

namespace nohn\Watermeter;

use Imagick;
use ImagickDraw;
use ImagickPixel;
use nohn\Watermeter\Debug;

class Watermeter
{
    /** @var array<string, mixed> */
    protected array $config = array();

    protected Imagick $sourceImage;

    protected ImagickPixel $strokeColor;

    protected float $strokeOpacity = 0.7;

    protected Imagick $sourceImageDebug;

    protected float $lastValue = 0;
    protected int $lastValueTimestamp;

    protected bool $debug = false;

    /**
     * @param bool $debug
     * @param array<string, mixed>|false $config
     * @param float|false $lastValue
     */
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

        if (isset($this->config['sourceImageRotate']) && is_numeric($this->config['sourceImageRotate']) && (float)$this->config['sourceImageRotate'] != 0.0) {
            $sourceImageTmp = clone $this->sourceImage;
            $sourceImageTmp->rotateImage('white', (float)$this->config['sourceImageRotate']);
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

            if ($sourceImageTmp->getImageWidth() < ($this->config['sourceImageCropStartX'] + $this->config['sourceImageCropSizeX'])) {
                $this->config['sourceImageCropStartX'] = 0;
                $this->config['sourceImageCropSizeX'] = 0;
            }
            if ($sourceImageTmp->getImageHeight() < ($this->config['sourceImageCropStartY'] + $this->config['sourceImageCropSizeY'])) {
                $this->config['sourceImageCropStartY'] = 0;
                $this->config['sourceImageCropSizeY'] = 0;
            }

            $sourceImageTmp->cropImage(
                (int)$this->config['sourceImageCropSizeX'],
                (int)$this->config['sourceImageCropSizeY'],
                (int)$this->config['sourceImageCropStartX'],
                (int)$this->config['sourceImageCropStartY']
            );
            $sourceImageTmp->setImagePage($sourceImageTmp->getImageWidth(), $sourceImageTmp->getImageHeight(), 0, 0);
            $this->sourceImage = clone $sourceImageTmp;
        }

        if (
            (isset($this->config['sourceImageBrightness']) && is_numeric($this->config['sourceImageBrightness'])) ||
            (isset($this->config['sourceImageContrast']) && is_numeric($this->config['sourceImageContrast']))
        ) {
            $sourceImageTmp = clone $this->sourceImage;
            $brightness = $this->config['sourceImageBrightness'] ?? 0;
            $contrast = $this->config['sourceImageContrast'] ?? 0;
            if (is_numeric($brightness) && is_numeric($contrast)) {
                $sourceImageTmp->brightnessContrastImage((float)$brightness, (float)$contrast);
            }
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

    public function writeSourceImage(string $path): void
    {
        $this->sourceImage->writeImage($path);
    }

    public function writeDebugImage(string $path): void
    {
        $this->sourceImageDebug->writeImage($path);
    }

    /**
     * @param array<string, mixed> $gauge
     */
    public function drawDebugImageGauge(array $gauge): void
    {
        $draw = new ImagickDraw();
        $draw->setStrokeColor($this->strokeColor);
        $draw->setStrokeOpacity($this->strokeOpacity);
        $draw->setStrokeWidth(1);
        $draw->setFillOpacity(0);
        $x = $gauge['x'] ?? 0;
        $y = $gauge['y'] ?? 0;
        $width = $gauge['width'] ?? 0;
        $height = $gauge['height'] ?? 0;
        if (is_numeric($x) && is_numeric($y) && is_numeric($width) && is_numeric($height)) {
            $draw->rectangle((float)$x, (float)$y, (float)$x + (float)$width, (float)$y + (float)$height);
            $draw->line((float)$x, (float)$y, (float)$x + (float)$width, (float)$y + (float)$height);
            $draw->line((float)$x, (float)$y + (float)$height, (float)$x + (float)$width, (float)$y);
        }
        $this->sourceImageDebug->drawImage($draw);
    }

    /**
     * @param array<string, mixed> $digit
     */
    public function drawDebugImageDigit(array $digit): void
    {
        $draw = new ImagickDraw();
        $draw->setStrokeColor($this->strokeColor);
        $draw->setStrokeOpacity($this->strokeOpacity);
        $draw->setStrokeWidth(1);
        $draw->setFillOpacity(0);
        $x = $digit['x'] ?? 0;
        $y = $digit['y'] ?? 0;
        $width = $digit['width'] ?? 0;
        $height = $digit['height'] ?? 0;
        if (is_numeric($x) && is_numeric($y) && is_numeric($width) && is_numeric($height)) {
            $draw->rectangle((float)$x, (float)$y, (float)$x + (float)$width, (float)$y + (float)$height);
        }
        $this->sourceImageDebug->drawImage($draw);
    }
}