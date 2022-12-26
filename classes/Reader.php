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
use nohn\AnalogMeterReader\AnalogMeter;
use thiagoalessio\TesseractOCR\TesseractOCR;

class Reader extends Watermeter
{
    private $hasErrors = false;

    private $errors = array();

    public function getValue()
    {
        return (float)($this->getReadout() + $this->getOffset());
    }

    public function getReadout()
    {
        if (isset($this->config['postDecimalDigits']) && !empty($this->config['postDecimalDigits'])) {
            $value = $this->readDigits() . '.' . $this->readDigits(true) . $this->readGauges();
        } else {
            $value = $this->readDigits() . '.' . $this->readGauges();
        }
        if (
            is_numeric($value) &&
            ($this->lastValue <= $value) &&
            (($value - $this->lastValue) < $this->config['maxThreshold'])
        ) {
            return $value;
        } else {
            $this->errors['getReadout() : is_numeric()'] = is_numeric($value);
            $this->errors['getReadout() : increasing'] = ($this->lastValue <= $value);
            $this->errors['value'] = $value;
            $this->errors['lastValue'] = $this->lastValue;
            $this->errors['delta'] = ($value - $this->lastValue);
            $this->hasErrors = true;
            return (float)$this->lastValue;
        }
    }

    private function readDigits($post_decimal = false)
    {
        $digitalSourceImage = clone $this->sourceImage;
        $targetImage = new Imagick();

        if ($post_decimal == false) {
            $digits_to_read = $this->config['digitalDigits'];
            $debug_image_path = 'pre_decimal';
        } else {
            $digits_to_read = $this->config['postDecimalDigits'];
            $debug_image_path = 'post_decimal';
        }

        foreach ($digits_to_read as $digit) {
            $rawDigit = clone $digitalSourceImage;
            $rawDigit->cropImage($digit['width'], $digit['height'], $digit['x'], $digit['y']);
            $targetImage->addImage($rawDigit);
            if ($this->debug) {
                $this->drawDebugImageDigit($digit);
            }
        }
        $targetImage->resetIterator();
        $numberDigitalImage = $targetImage->appendImages(false);
        if (isset($this->config['digitDecolorization']) && $this->config['digitDecolorization']) {
            $numberDigitalImage->modulateImage(100, 0, 100);
        }
        if (!isset($this->config['postprocessing']) || (isset($this->config['postprocessing']) && $this->config['postprocessing'])) {
            $numberDigitalImage->enhanceImage();
            $numberDigitalImage->equalizeImage();
        }
        if (isset($this->config['digitalDigitsInversion']) && $this->config['digitalDigitsInversion']) {
            $numberDigitalImage->negateImage(false);
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
            $numberDigitalImage->writeImage('tmp/'.$debug_image_path.'_digital.jpg');
            echo "Raw OCR: $numberOCR<br>";
            echo "Clean OCR: $numberDigital";
            echo '<img alt="Digital Preview" src="tmp/'.$debug_image_path.'_digital.jpg" /><br>';
        }

        if (is_numeric($numberDigital)) {
            $preDecimalPlaces = (int)$numberDigital;
        } else {
            $preDecimalPlaces = (int)$this->lastValue;
            if ($this->debug) {
                echo 'Choosing last value ' . $preDecimalPlaces . '<br>';
            }
            $this->errors['readDigits() : !is_numeric()'] = 'Could not interpret "' . $numberDigital . '". Using last known value ' . (int)$this->lastValue;
            $this->hasErrors = true;
        }
        if ($this->debug) {
            echo "Digital: $preDecimalPlaces<br>";
            echo '<table border="1"><tr>';
            echo '<td>';
            $digitalSourceImage->writeImage('tmp/input.jpg');
            $numberDigitalImage->writeImage('tmp/'.$debug_image_path.'_digital.png');
            echo '</td>';
        }
        return $preDecimalPlaces;
    }

    private function readGauges()
    {
        $decimalPlaces = null;
        foreach ($this->config['analogGauges'] as $gaugeKey => $gauge) {
            $gauge['key'] = $gaugeKey;
            $rawGaugeImage = clone $this->sourceImage;
            $rawGaugeImage->cropImage($gauge['width'], $gauge['height'], $gauge['x'], $gauge['y']);
            $rawGaugeImage->setImagePage(0, 0, 0, 0);
            $amr = new AnalogMeter($rawGaugeImage, 'r');
            $decimalPlaces .= $amr->getValue();
            if ($this->debug) {
                $this->debugGauge($amr, $gauge);
            }
        }
        return $decimalPlaces;
    }

    private function debugGauge($amr, $gauge)
    {
        $this->drawDebugImageGauge($gauge);
        echo '<td>';
        echo $amr->getValue(true) . '<br>';
        echo '<img src="tmp/analog_' . $gauge['key'] . '.png" /><br />';
        $debugData = $amr->getDebugData();
        foreach ($debugData as $significance => $step) {
            echo round($significance, 4) . ': ' . $step['xStep'] . 'x' . $step['yStep'] . ' => ' . $step['number'] . '<br>';
        }
        $debugImage = $amr->getDebugImage();
        $debugImage->setImageFormat('png');
        $debugImage->writeImage(__DIR__ . '/../public/tmp/analog_' . $gauge['key'] . '.png');
        echo '</td>';
    }

    public function getOffset()
    {
        if (isset($this->config['offsetValue'])) {
            return (float)$this->config['offsetValue'];
        } else {
            return 0;
        }
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