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

use nohn\AnalogMeterReader\AnalogMeter;
use Imagick;
use thiagoalessio\TesseractOCR\TesseractOCR;

class Reader extends Watermeter
{
    private $hasErrors = false;

    private $errors = array();

    private function readGauges()
    {
        $decimalPlaces = null;
        foreach ($this->config['analogGauges'] as $gaugeKey => $gauge) {
            if ($this->debug) {
                $this->drawDebugImageGauge($gauge);
            }
            $rawGaugeImage = clone $this->sourceImage;
            $rawGaugeImage->cropImage($gauge['width'], $gauge['height'], $gauge['x'], $gauge['y']);
            $rawGaugeImage->setImagePage(0, 0, 0, 0);
            $amr = new AnalogMeter($rawGaugeImage, 'r');
            $decimalPlaces .= $amr->getValue();
            if ($this->debug) {
                echo '<td>';
                echo $amr->getValue($this->debug) . '<br>';
                echo '<img src="tmp/analog_' . $gaugeKey . '.png" /><br />';
                $debugData = $amr->getDebugData();
                foreach ($debugData as $significance => $step) {
                    echo round($significance, 4) . ': ' . $step['xStep'] . 'x' . $step['yStep'] . ' => ' . $step['number'] . '<br>';
                }
                $debugImage = $amr->getDebugImage();
                $debugImage->setImageFormat('png');
                $debugImage->writeImage(__DIR__ . '/../public/tmp/analog_' . $gaugeKey . '.png');
                echo '</td>';
            }
        }
        return $decimalPlaces;
    }

    private function readDigits()
    {
        $digitalSourceImage = clone $this->sourceImage;
        $targetImage = new Imagick();

        foreach ($this->config['digitalDigits'] as $digit) {
            $rawDigit = clone $digitalSourceImage;
            $rawDigit->cropImage($digit['width'], $digit['height'], $digit['x'], $digit['y']);
            $targetImage->addImage($rawDigit);
            if ($this->debug) {
                $this->drawDebugImageDigit($digit);
            }
        }
        $targetImage->resetIterator();
        $numberDigitalImage = $targetImage->appendImages(false);
        if (!isset($this->config['postprocessing']) || (isset($this->config['postprocessing']) && $this->config['postprocessing'])) {
            $numberDigitalImage->enhanceImage();
            $numberDigitalImage->equalizeImage();
        }
        $numberDigitalImage->setImageFormat("png");
        $numberDigitalImage->borderImage('white', 10, 10);

        $ocr = new TesseractOCR();
        $ocr->imageData($numberDigitalImage, sizeof($numberDigitalImage));
        $ocr->allowlist(range('0', '9'));
        $numberOCR = $ocr->run();
        $numberDigital = preg_replace('/\s+/', '', $numberOCR);
        // There is TesseractOCR::digits(), but sometimes this will not convert a letter do a similar looking digit but completely ignore it. So we replace o with 0, I with 1 etc.
        $numberDigital = strtr($numberDigital, 'oOiIlzZsSBg', '00111225589');
        // $numberDigital = '00815';
        if ($this->debug) {
            $numberDigitalImage->writeImage('tmp/digital.jpg');
            echo "Raw OCR: $numberOCR<br>";
            echo "Clean OCR: $numberDigital";
            echo '<img alt="Digital Preview" src="tmp/digital.jpg" /><br>';
        }

        if (is_numeric($numberDigital)) {
            $preDecimalPlaces = (int)$numberDigital;
        } else {
            $preDecimalPlaces = (int)$this->lastValue;
            if ($this->debug) {
                echo 'Choosing last value ' . $preDecimalPlaces . '<br>';
            }
            $this->errors[__LINE__] = 'Could not interpret ' . $numberDigital . '. Using last known value ' . (int)$this->lastValue;
            $this->hasErrors = true;
        }
        if ($this->debug) {
            echo "Digital: $preDecimalPlaces<br>";
            echo '<table border="1"><tr>';
            echo '<td>';
            $digitalSourceImage->writeImage('tmp/input.jpg');
            $numberDigitalImage->writeImage('tmp/digital.png');
            echo '</td>';
        }
        return $preDecimalPlaces;
    }

    public function getReadout()
    {
        $value = $this->readDigits() . '.' . $this->readGauges();
        if (
            is_numeric($value) &&
            ($this->lastValue <= $value) &&
            (($value - $this->lastValue) < $this->config['maxThreshold'])
        ) {
            return $value;
        } else {
            $this->errors[__LINE__] = is_numeric($value);
            $this->errors[__LINE__] = ($this->lastValue <= $value);
            $this->errors[__LINE__][] = ($value - $this->lastValue < 1);
            $this->errors[__LINE__][] = $value;
            $this->errors[__LINE__][] = $this->lastValue;
            $this->errors[__LINE__][] = ($value - $this->lastValue);
            $this->hasErrors = true;
            return $this->lastValue;
        }
    }

    public function getOffset()
    {
        if (isset($this->config['offsetValue'])) {
            return $this->config['offsetValue'];
        } else {
            return 0;
        }
    }

    public function getValue()
    {
        return $this->getReadout() + $this->getOffset();
    }

    public function hasErrors()
    {
        return $this->hasErrors;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}