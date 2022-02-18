<?php
require __DIR__ . '/../vendor/autoload.php';

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

try {
    $logGaugeImages = array();
    $value = $watermeterReader->read($fullDebug);

    $returnData = array();
    if ($watermeterReader->hasErrors()) {
        $returnData['value'] = $lastValue;
        $returnData['status'] = 'error';
        $returnData['errors'] = $watermeterReader->getErrors();
        $returnData['exception'] = false;
        $returnData['lastUpdated'] = $lastValueTimestamp;
    } else {
        $returnData['value'] = $value;
        $returnData['status'] = 'success';
        $returnData['errors'] = false;
        $returnData['exception'] = false;
        $returnData['lastUpdated'] = $now;
        file_put_contents('../src/config/lastValue.txt', $value);
    }
    if ($fullDebug) {
        echo '<td>';
        $watermeterReader->writeDebugImage(__DIR__ . '/../public/tmp/input_debug.jpg');
        echo '<img src="tmp/input_debug.jpg" />';
        echo '</td>';
        echo '</tr></table>';
        echo "hasErrors: " . $watermeterReader->hasErrors() . "\n<br>";
        echo "<pre>";
        var_dump($watermeterReader->getErrors());
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
