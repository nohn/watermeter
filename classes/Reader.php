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
 * @copyright 2026 Sebastian Nohn
 * @license   http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License version 3.0
 */

namespace nohn\Watermeter;

use Imagick;
use nohn\AnalogMeterReader\AnalogMeter;
use thiagoalessio\TesseractOCR\TesseractOCR;
use thiagoalessio\TesseractOCR\TesseractOcrException;

class Reader extends Watermeter
{
    private bool $hasErrors = false;

    /** @var array<string, mixed> */
    private array $errors = array();

    public function getValue(): float
    {
        return (float)($this->getReadout() + $this->getOffset());
    }

    public function getReadout(): float
    {
        if (isset($this->config['postDecimalDigits']) && !empty($this->config['postDecimalDigits']) &&
            isset($this->config['analogGauges']) && !empty($this->config['analogGauges'])) {
            $value = $this->readDigits() . '.' . $this->readDigits(true) . $this->readGauges();
        } else if (isset($this->config['analogGauges']) && !empty($this->config['analogGauges'])) {
            $value = $this->readDigits() . '.' . $this->readGauges();
        } else if (isset($this->config['postDecimalDigits']) && !empty($this->config['postDecimalDigits'])) {
            $value = $this->readDigits() . '.' . $this->readDigits(true);
        } else {
            $value = $this->readDigits();
        }
        if (
            is_numeric($value) &&
            ($this->lastValue <= (float)$value) &&
            (((float)$value - $this->lastValue) <= $this->config['maxThreshold'])
        ) {
            return (float)$value;
        } else {
            $this->errors['getReadout() : is_numeric()'] = is_numeric($value);
            $this->errors['getReadout() : increasing'] = ($this->lastValue <= (float)$value);
            $this->errors['value'] = $value;
            $this->errors['lastValue'] = $this->lastValue;
            $this->errors['delta'] = ((float)$value - $this->lastValue);
            $this->hasErrors = true;
            return $this->lastValue;
        }
    }

    /**
     * @param bool $post_decimal
     */
    private function readDigits($post_decimal = false): int
    {
        $digitalSourceImage = clone $this->sourceImage;
        $targetImage = new Imagick();

        $digits_to_read = array();
        if ($post_decimal == false) {
            if (isset($this->config['digitalDigits']) && is_array($this->config['digitalDigits'])) {
                $digits_to_read = $this->config['digitalDigits'];
            }
            $cachePrefix = '';
        } else {
            if (isset($this->config['postDecimalDigits']) && is_array($this->config['postDecimalDigits'])) {
                $digits_to_read = $this->config['postDecimalDigits'];
            }
            $cachePrefix = 'post_decimal';
        }

        /** @var array<string, mixed> $digit */
        foreach ($digits_to_read as $digit) {
            $rawDigit = clone $digitalSourceImage;
            $width = $digit['width'] ?? 0;
            $height = $digit['height'] ?? 0;
            $x = $digit['x'] ?? 0;
            $y = $digit['y'] ?? 0;
            if (is_numeric($width) && (int)$width > 0 && is_numeric($height) && (int)$height > 0 && is_numeric($x) && is_numeric($y)) {
                $rawDigit->cropImage((int)$width, (int)$height, (int)$x, (int)$y);
                $targetImage->addImage($rawDigit);
                if ($this->debug) {
                    $this->drawDebugImageDigit($digit);
                }
            }
        }
        $targetImage->resetIterator();
        $numberDigitalImage = $targetImage->appendImages(false);
        if (isset($this->config['digitDecolorization']) && $this->config['digitDecolorization']) {
            $numberDigitalImage->modulateImage(100, 0, 100);
        }
        if (!isset($this->config['postprocessing']) || $this->config['postprocessing']) {
            $numberDigitalImage->enhanceImage();
            $numberDigitalImage->equalizeImage();
        }
        if (isset($this->config['digitalDigitsInversion']) && $this->config['digitalDigitsInversion']) {
            $numberDigitalImage->negateImage(false);
        }
        $numberDigitalImage->setImageFormat("png");
        $numberDigitalImage->borderImage('white', 10, 10);
        try {
            $ocr = new TesseractOCR();
            $ocr->imageData($numberDigitalImage, count($numberDigitalImage));
            $ocr->allowlist(range('0', '9'));
            $numberOCR = (string)$ocr->run();
        } catch (TesseractOcrException $e) {
            $numberOCR = '';
            $this->errors['TesseractOcrException'] = $e->getMessage();
        }
        $numberDigital = (string)preg_replace('/\s+/', '', $numberOCR);
        // There is TesseractOCR::digits(), but sometimes this will not convert a letter do a similar looking digit but completely ignore it. So we replace o with 0, I with 1 etc.
        $numberDigital = strtr($numberDigital, 'oOiIlzZsSBg', '00111225589');
        // $numberDigital = '00815';
        if ($this->debug) {
            $numberDigitalImage->writeImage('tmp/' . $cachePrefix . '_digital.jpg');
            echo "Raw OCR: $numberOCR<br>";
            echo "Clean OCR: $numberDigital";
            echo '<img alt="Digital Preview" src="tmp/' . $cachePrefix . '_digital.jpg" /><br>';
        }

        if (is_numeric($numberDigital)) {
            $numberRead = (int)$numberDigital;
        } else {
            # FIXXME
            $numberRead = (int)$this->lastValue;
            if ($this->debug) {
                echo 'Choosing last value ' . $numberRead . '<br>';
            }
            $this->errors['readDigits() : !is_numeric()'] = 'Could not interpret "' . (string)$numberDigital . '". Using last known value ' . (int)$this->lastValue;
            $this->hasErrors = true;
        }
        if ($this->debug) {
            echo "Digital: $numberRead<br>";
            echo '<table border="1"><tr>';
            echo '<td>';
            $digitalSourceImage->writeImage('tmp/input.jpg');
            $numberDigitalImage->writeImage('tmp/' . $cachePrefix . '_digital.png');
            echo '</td>';
        }
        return $numberRead;
    }

    private function readGauges(): string
    {
        $decimalPlaces = '';
        $analogGauges = array();
        if (isset($this->config['analogGauges']) && is_array($this->config['analogGauges'])) {
            $analogGauges = $this->config['analogGauges'];
        }
        /** @var array<string, mixed> $gauge */
        foreach ($analogGauges as $gaugeKey => $gauge) {
            $gauge['key'] = (string)$gaugeKey;
            $rawGaugeImage = clone $this->sourceImage;
            $width = $gauge['width'] ?? 0;
            $height = $gauge['height'] ?? 0;
            $x = $gauge['x'] ?? 0;
            $y = $gauge['y'] ?? 0;
            if (is_numeric($width) && is_numeric($height) && is_numeric($x) && is_numeric($y)) {
                $rawGaugeImage->cropImage((int)$width, (int)$height, (int)$x, (int)$y);
            }
            $rawGaugeImage->setImagePage(0, 0, 0, 0);
            $amr = new AnalogMeter($rawGaugeImage, 'r');
            $decimalPlaces .= $amr->getValue();
            if ($this->debug) {
                $this->debugGauge($amr, $gauge);
            }
        }
        return $decimalPlaces;
    }

    /**
     * @param AnalogMeter $amr
     * @param array<string, mixed> $gauge
     */
    private function debugGauge($amr, $gauge): void
    {
        $this->drawDebugImageGauge($gauge);
        echo '<td>';
        echo $amr->getValue(true) . '<br>';
        $gaugeKey = $gauge['key'] ?? '';
        if (is_scalar($gaugeKey) && (string)$gaugeKey !== '') {
            echo '<img src="tmp/analog_' . (string)$gaugeKey . '.png" /><br />';
        }
        $debugData = $amr->getDebugData();
        foreach ($debugData as $significance => $step) {
            echo round($significance, 4) . ': ' . $step['xStep'] . 'x' . $step['yStep'] . ' => ' . $step['number'] . '<br>';
        }
        $debugImage = $amr->getDebugImage();
        $debugImage->setImageFormat('png');
        $gaugeKey = $gauge['key'] ?? '';
        if (is_scalar($gaugeKey) && (string)$gaugeKey !== '') {
            $debugImage->writeImage(__DIR__ . '/../public/tmp/analog_' . (string)$gaugeKey . '.png');
        }
        echo '</td>';
    }

    public function getOffset(): float
    {
        $offsetValue = $this->config['offsetValue'] ?? 0;
        if (is_numeric($offsetValue)) {
            return (float)$offsetValue;
        } else {
            return 0;
        }
    }

    public function hasErrors(): bool
    {
        return $this->hasErrors;
    }

    /**
     * @return array<string, mixed>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
