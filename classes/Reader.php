<?php

namespace nohn\Watermeter;

use nohn\AnalogMeterReader\AnalogMeter;
use Imagick;
use ImagickDraw;
use thiagoalessio\TesseractOCR\TesseractOCR;

class Reader extends Watermeter
{
    private $hasErrors = false;

    public function readGauges($fullDebug = false)
    {
        $decimalPlaces = null;
        foreach ($this->config['analogGauges'] as $gaugeKey => $gauge) {
            if ($fullDebug) {
                echo '<td>';
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
            $rawGaugeImage = clone $this->sourceImage;
            $rawGaugeImage->cropImage($gauge['width'], $gauge['height'], $gauge['x'], $gauge['y']);
            $rawGaugeImage->setImagePage(0, 0, 0, 0);
            if (isset($config['logging']) && $config['logging']) {
                $logGaugeImages[] = $rawGaugeImage;
            }
            $amr = new AnalogMeter($rawGaugeImage, 'r');
            $decimalPlaces .= $amr->getValue();
            if ($fullDebug) {
                echo $amr->getValue($fullDebug) . '<br>';
                echo '<img src="tmp/analog_' . $gaugeKey . '.png" /><br />';
                $debugData = $amr->getDebugData();
                foreach ($debugData as $significance => $step) {
                    echo round($significance, 4) . ': ' . $step['xStep'] . 'x' . $step['yStep'] . ' => ' . $step['number'] . '<br>';
                }
                $debugImage = $amr->getDebugImage();
                $debugImage->setImageFormat('png');
                $debugImage->writeImage(__DIR__.'/../public/tmp/analog_' . $gaugeKey . '.png');
                echo '</td>';
            }
        }
        return $decimalPlaces;
    }

    public function readDigits($fullDebug = false)
    {
        $digitalSourceImage = clone $this->sourceImage;
        $targetImage = new Imagick();

        foreach ($this->config['digitalDigits'] as $digit) {
            $rawDigit = clone $digitalSourceImage;
            $rawDigit->cropImage($digit['width'], $digit['height'], $digit['x'], $digit['y']);
            $targetImage->addImage($rawDigit);
            if ($fullDebug) {
                $draw = new ImagickDraw();
                $draw->setStrokeColor($this->strokeColor);
                $draw->setStrokeOpacity($this->strokeOpacity);
                $draw->setStrokeWidth(1);
                $draw->setFillOpacity(0);
                $draw->rectangle($digit['x'], $digit['y'], $digit['x'] + $digit['width'], $digit['y'] + $digit['height']);
                $this->sourceImageDebug->drawImage($draw);
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
        // There is TesseractOCR::digits(), but sometimes this will not convert a letter do a similar looking digit but completly ignore it. So we replace o with 0, I with 1 etc.
        $numberDigital = strtr($numberDigital, 'oOiIlzZsSBg', '00111225589');
        // $numberDigital = '00815';
        if ($fullDebug) {
            $numberDigitalImage->writeImage('tmp/digital.jpg');
            echo "Raw OCR: $numberOCR<br>";
            echo "Clean OCR: $numberDigital";
            echo '<img alt="Digital Preview" src="tmp/digital.jpg" /><br>';
        }

        if (is_numeric($numberDigital)) {
            $preDecimalPlaces = (int)$numberDigital;
        } else {
            $preDecimalPlaces = (int)$this->lastValue;
            if ($fullDebug) {
                echo 'Choosing last value ' . $preDecimalPlaces . '<br>';
            }
            $errors[__LINE__] = 'Could not interpret ' . $numberDigital . '. Using last known value ' . $lastPreDecimalPlaces;
        }
        if ($fullDebug) {
            echo "Digital: $preDecimalPlaces<br>";
            echo '<table border="1"><tr>';
            echo '<td>';
            $digitalSourceImage->writeImage('tmp/input.jpg');
            $numberDigitalImage->writeImage('tmp/digital.png');
            echo '</td>';
        }
        return $preDecimalPlaces;
    }

    public function read($fullDebug = false) {
        $value = $this->readDigits() . '.' . $this->readGauges();
        return $value;
    }
}