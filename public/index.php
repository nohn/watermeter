<?php
require __DIR__ . '/../vendor/autoload.php';
require '../src/config/config.php';

use thiagoalessio\TesseractOCR\TesseractOCR;
use nohn\Watermeter\Cache;
use nohn\Watermeter\Reader;

$watermeterCache = new Cache();
$lastValue = $watermeterCache->getValue();
$lastValueTimestamp = $watermeterCache->getLastUpdate();

$watermeterReader = new Reader();

$lastPreDecimalPlaces = (int)$lastValue;

if (isset($_GET['debug'])) {
    $fullDebug = true;
} else {
    $fullDebug = false;
}

$now = time();

$strokeColor = new ImagickPixel('white');
$strokeOpacity = 0.7;

try {


    $logGaugeImages = array();

    if ($fullDebug) {
        echo '<td>';
        $sourceImageDebug->writeImage('tmp/input_debug.jpg');
        echo '<img src="tmp/input_debug.jpg" />';
        echo '</td>';
        echo '</tr></table>';
    }
    $value = $watermeterReader->read();

    if (isset($config['logging']) && $config['logging'] && ($lastValue != $value)) {
        $numberDigitalImage->setImageFormat('png');
        $numberDigitalImage->writeImage('tmp/' . $now . '_' . $lastValue . '-' . $value . '_digital.png');
        for ($i = 0; $i < sizeof($logGaugeImages); $i++) {
            $logGaugeImages[$i]->setImageFormat('png');
            $logGaugeImages[$i]->writeImage('tmp/' . $now . '_' . $lastValue . '-' . $value . '_analog_' . ($i + 1) . '_input.png');
        }
    }

    if (
        is_numeric($value) &&
        ($lastValue <= $value) &&
        (($value - $lastValue) < $config['maxThreshold'])
    ) {
    } else {
        $errors[__LINE__] = is_numeric($value);
        $errors[__LINE__] = ($lastValue <= $value);
        $errors[__LINE__][] = ($value - $lastValue < 1);
        $errors[__LINE__][] = $value;
        $errors[__LINE__][] = $lastValue;
        $errors[__LINE__][] = ($value - $lastValue);
        $hasErrors = true;
    }
    $returnData = array();
    if ($hasErrors) {
        $returnData['value'] = $lastValue;
        $returnData['status'] = 'error';
        $returnData['errors'] = $errors;
        $returnData['exception'] = false;
        $returnData['lastUpdated'] = $lastValueTimestamp;
    } else {
        $returnData['value'] = $value;
        $returnData['status'] = 'error';
        $returnData['errors'] = false;
        $returnData['exception'] = false;
        $returnData['lastUpdated'] = $now;
        file_put_contents('../src/config/lastValue.txt', $value);
    }
    if ($fullDebug) {
        echo "hasErrors: $hasErrors\n<br>";
        echo "<pre>";
        var_dump($errors);
        echo "</pre>";
        echo "lastValue: $lastValue\n<br>";
        echo "value: $value\n<br>";
    }
    if (isset($_GET['json'])) {
        header("Content-Type: application/json");
        echo json_encode($returnData);
    } else {
        echo $returnData['value'];
    }
} catch (Exception $e) {
    if (isset($config['logging']) && $config['logging']) {
        file_put_contents('../log/error/' . $now . '_exception.txt', $e->__toString());
    }
    $returnData = array(
        'value' => $lastValue,
        'status' => 'exception',
        'errors' => false,
        'exception' => $e->__toString(),
        'lastUpdated' => $lastValueTimestamp,
    );
    if (isset($_GET['json'])) {
        header("Content-Type: application/json");
        echo json_encode($returnData);
    } else {
        echo $returnData['value'];
    }
}
