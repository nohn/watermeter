<?php
require __DIR__ . '/../vendor/autoload.php';
require 'config/config.php';

$lastValue = file_get_contents('config/lastValue.txt');

use thiagoalessio\TesseractOCR\TesseractOCR;
use nohn\AnalogMeterReader\AnalogMeter;

$fields = array('x', 'y', 'width', 'height');
if (isset($_POST['sourceImage'])) {
    $config['sourceImage'] = $_POST['sourceImage'];
}
if (isset($_POST['maxThreshold'])) {
    $config['maxThreshold'] = $_POST['maxThreshold'];
}
if (isset($_POST['lastValue'])) {
    $lastValue = $_POST['lastValue'];
}
if (isset($_POST['digit'])) {
    $config['digitalDigits'] = $_POST['digit'];
}
if (isset($_POST['gauge'])) {
    $config['analogGauges'] = $_POST['gauge'];
}
if (isset($_POST) && ($_POST['action'] == 'save')) {
    $newConfig = var_export($config, true);
    file_put_contents('config/config.php', "<?php\n\$config = " . $newConfig . ";");
    file_put_contents('config/lastValue.txt', $lastValue);
}
?>
<html>
<head>
    <title>Watermeter - Configure</title>
    <style type="text/css">
        .base input {
            float: left;
            width: 20em;
        }

        .base legend[for] {
            float: left;
            width: 7em;
            clear: both;
        }

        .coordinates input,
        .coordinates legend[for] {
            float: left;
            width: 3em;
            text-align: right;
        }
    </style>
</head>
<body>
<?php switch ($_GET['action']): ?>
<?php default: ?>
<form method="post" style="float: left;">
    <fieldset class="base">
        <legend>Base Settings</legend>
        <legend for="sourceImage">Source Image</legend>
        <input type="text" name="sourceImage" id="sourceImage" value="<?php echo $config['sourceImage']; ?>">
        <legend for="maxThreshold">Max. Threshold</legend>
        <input type="text" name="maxThreshold" id="maxThreshold" value="<?php echo $config['maxThreshold']; ?>">
        <legend for="lastValue">Initial Value</legend>
        <input type="text" name="lastValue" id="lastValue" value="<?php echo $lastValue ?>">
    </fieldset>
    <?php
    $strokeColor = new ImagickPixel('white');
    $strokeOpacity = 0.7;

    $sourceImageDebug = new Imagick($config['sourceImage']);
    $targetImage = new Imagick();
    echo '<fieldset class="coordinates"><legend>Digital Digits</legend>';
    foreach ($config['digitalDigits'] as $key => $digit) {
        echo '<fieldset><legend>' . $key . '</legend>';
        foreach ($fields as $field) {
            echo '<legend for="digit[' . $key . '][' . $field . ']">' . $field . '</legend><input name="digit[' . $key . '][' . $field . ']" id="digit[' . $key . '][' . $field . ']" type="text" value="' . $digit[$field] . '">';
        }
        echo '</fieldset>';
        $draw = new ImagickDraw();
        $draw->setStrokeColor($strokeColor);
        $draw->setStrokeOpacity($strokeOpacity);
        $draw->setStrokeWidth(1);
        $draw->setFillOpacity(0);
        $draw->rectangle($digit['x'], $digit['y'], $digit['x'] + $digit['width'], $digit['y'] + $digit['height']);
        $sourceImageDebug->drawImage($draw);
    }
    echo '</fieldset>';

    echo '<fieldset class="coordinates"><legend>Analog Digits</legend>';
    foreach ($config['analogGauges'] as $key => $gauge) {
        echo '<fieldset><legend>' . $key . '</legend>';
        foreach ($fields as $field) {
            echo '<legend for="gauge[' . $key . '][' . $field . ']">' . $field . '</legend><input name="gauge[' . $key . '][' . $field . ']" id="gauge[' . $key . '][' . $field . ']" type="text" value="' . $gauge[$field] . '">';
        }
        echo '</fieldset>';
        $draw = new ImagickDraw();
        $draw->setStrokeColor($strokeColor);
        $draw->setStrokeOpacity($strokeOpacity);
        $draw->setStrokeWidth(1);
        $draw->setFillOpacity(0);
        $draw->rectangle($gauge['x'], $gauge['y'], $gauge['x'] + $gauge['width'], $gauge['y'] + $gauge['height']);
        $draw->line($gauge['x'], $gauge['y'], $gauge['x'] + $gauge['width'], $gauge['y'] + $gauge['height']);
        $draw->line($gauge['x'], $gauge['y'] + $gauge['height'], $gauge['x'] + $gauge['width'], $gauge['y']);
        $sourceImageDebug->drawImage($draw);
    }
    echo '</fieldset>';
    $sourceImageDebug->writeImage('debug/input_debug.jpg');
    echo '<input type="submit" name="action" value="preview">';
    echo '<input type="submit" name="action" value="save">';
    echo '</form>';
    echo '<img src="debug/input_debug.jpg" style="float: left;"/>';
    ?>
    <?php endswitch; ?>
</body>
</html>