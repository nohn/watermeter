<?php

namespace nohn\Watermeter;

use nohn\AnalogMeterReader\AnalogMeter;
use ImagickDraw;

class Reader extends Watermeter
{
    public function readAnalogGauges($fullDebug = false)
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
}