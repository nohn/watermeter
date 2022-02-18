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

require __DIR__ . '/../vendor/autoload.php';

use nohn\Watermeter\Cache;
use nohn\Watermeter\Reader;
use nohn\Watermeter\Config;

$watermeterCache = new Cache();
$lastValue = $watermeterCache->getValue();

$watermeterConfig = new Config();
$config = $watermeterConfig->get();

$fields = array('x', 'y', 'width', 'height');
if (isset($_POST['sourceImage'])) {
    $config['sourceImage'] = $_POST['sourceImage'];
}
if (isset($_POST['sourceImageRotate'])) {
    $config['sourceImageRotate'] = $_POST['sourceImageRotate'];
}
if (isset($_POST['sourceImageCropStartX'])) {
    $config['sourceImageCropStartX'] = $_POST['sourceImageCropStartX'];
}
if (isset($_POST['sourceImageCropStartY'])) {
    $config['sourceImageCropStartY'] = $_POST['sourceImageCropStartY'];
}
if (isset($_POST['sourceImageCropSizeX'])) {
    $config['sourceImageCropSizeX'] = $_POST['sourceImageCropSizeX'];
}
if (isset($_POST['sourceImageCropSizeY'])) {
    $config['sourceImageCropSizeY'] = $_POST['sourceImageCropSizeY'];
}
if (isset($_POST['sourceImageBrightness']) && $_POST['sourceImageBrightness'] != 'false') {
    $config['sourceImageBrightness'] = $_POST['sourceImageBrightness'];
}
if (isset($_POST['sourceImageContrast']) && $_POST['sourceImageContrast'] != 'false') {
    $config['sourceImageContrast'] = $_POST['sourceImageContrast'];
}
if (isset($_POST['sourceImageEqualize']) && ($_POST['sourceImageEqualize'] == 'on')) {
    $config['sourceImageEqualize'] = true;
} else {
    $config['sourceImageEqualize'] = false;
}
if (isset($_POST['maxThreshold'])) {
    $config['maxThreshold'] = $_POST['maxThreshold'];
}
if (isset($_POST['postprocessing']) && ($_POST['postprocessing'] == 'on')) {
    $config['postprocessing'] = true;
} else {
    $config['postprocessing'] = false;
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
if (isset($_POST['action']) && ($_POST['action'] == 'save')) {
    $watermeterConfig->set($config);
    $watermeterConfig->store();
    file_put_contents('../src/config/lastValue.txt', $lastValue);
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
            width: 15em;
            clear: both;
        }

        .coordinates input,
        .coordinates legend[for] {
            float: left;
            width: 3em;
            text-align: right;
        }
    </style>
    <script type="text/javascript">
        function removeElement(prefix) {
            allElements = document.querySelectorAll("fieldset[id^=" + prefix + "]");
            return document.getElementById(prefix + "_" + allElements.length).remove();
        }

        function addElement(prefix) {
            numberElements = document.querySelectorAll("fieldset[id^=" + prefix + "]").length;
            var form = document.getElementById('config');
            var input = document.createElement('input');
            input.setAttribute('name', prefix + '[' + (numberElements + 1) + ']');
            input.setAttribute('value', '0');
            input.setAttribute('type', 'hidden')
            form.appendChild(input);
            form.submit();
        }
    </script>
</head>
<body>
<form method="post" id="config" style="float: left;">
    <fieldset class="base">
        <legend>Base Settings</legend>
        <legend for="sourceImage">Source Image</legend>
        <input type="text" name="sourceImage" id="sourceImage" value="<?php echo $config['sourceImage']; ?>">
        <legend for="sourceImageRotate">Source Image Rotate °</legend>
        <input type="text" name="sourceImageRotate" id="sourceImageRotate" value="<?php echo $config['sourceImageRotate']; ?>">
        <legend for="sourceImageCropStartX">Source Image Crop Start x</legend>
        <input type="text" name="sourceImageCropStartX" id="sourceImageCropStartX" value="<?php echo $config['sourceImageCropStartX']; ?>">
        <legend for="sourceImageCropStartY">Source Image Crop Start y</legend>
        <input type="text" name="sourceImageCropStartY" id="sourceImageCropStartY" value="<?php echo $config['sourceImageCropStartY']; ?>">
        <legend for="sourceImageCropSizeX">Source Image Crop Width</legend>
        <input type="text" name="sourceImageCropSizeX" id="sourceImageCropSizeX" value="<?php echo $config['sourceImageCropSizeX']; ?>">
        <legend for="sourceImageCropSizeY">Source Image Crop Height</legend>
        <input type="text" name="sourceImageCropSizeY" id="sourceImageCropSizeY" value="<?php echo $config['sourceImageCropSizeY']; ?>">
        <legend for="sourceImageBrightness">Source Image Brightness Adjust (%)</legend>
        <input type="text" name="sourceImageBrightness" id="sourceImageBrightness" value="<?php echo $config['sourceImageBrightness']; ?>">
        <legend for="sourceImageContrast">Source Image Contrast Adjust (%)</legend>
        <input type="text" name="sourceImageContrast" id="sourceImageContrast" value="<?php echo $config['sourceImageContrast']; ?>">
        <legend for="sourceImageEqualize">Source Image histogram equalization</legend>
        <input type="checkbox" name="sourceImageEqualize"
               id="sourceImageEqualize" <?php echo $config['sourceImageEqualize'] == true ? 'checked' : ''; ?>>
        <legend for="maxThreshold">Max. Threshold</legend>
        <input type="text" name="maxThreshold" id="maxThreshold" value="<?php echo $config['maxThreshold']; ?>">
        <legend for="lastValue">Initial Value</legend>
        <input type="text" name="lastValue" id="lastValue" value="<?php echo $lastValue ?>">
        <legend for="postprocessing">Digit Postprocessing</legend>
        <input type="checkbox" name="postprocessing"
               id="postprocessing" <?php echo $config['postprocessing'] == true ? 'checked' : ''; ?>>
    </fieldset>
    <?php
    echo '<fieldset class="coordinates"><legend>Digital Digits</legend>';
    foreach ($config['digitalDigits'] as $key => $digit) {
        echo '<fieldset id="digit_' . $key . '"><legend>' . $key . '</legend>';
        foreach ($fields as $field) {
            echo '<legend for="digit[' . $key . '][' . $field . ']">' . $field . '</legend><input name="digit[' . $key . '][' . $field . ']" id="digit[' . $key . '][' . $field . ']" type="text" value="' . (isset($digit[$field]) ? $digit[$field] : '') . '">';
        }
        echo '</fieldset>';
    }
    echo '<button onclick="return removeElement(\'digit\')" />Remove a Digit</button>';
    echo '<button onclick="return addElement(\'digit\')" />Add a Digit</button>';
    echo '</fieldset>';
    echo '<fieldset class="coordinates"><legend>Analog Digits</legend>';
    foreach ($config['analogGauges'] as $key => $gauge) {
        echo '<fieldset id="gauge_' . $key . '"><legend>' . $key . '</legend>';
        foreach ($fields as $field) {
            echo '<legend for="gauge[' . $key . '][' . $field . ']">' . $field . '</legend><input name="gauge[' . $key . '][' . $field . ']" id="gauge[' . $key . '][' . $field . ']" type="text" value="' . (isset($gauge[$field]) ? $gauge[$field] : '') . '">';
        }
        echo '</fieldset>';
    }
    echo '<button onclick="return removeElement(\'gauge\')" />Remove a Gauge</button>';
    echo '<button onclick="return addElement(\'gauge\')" />Add a Gauge</button>';
    echo '</fieldset>';

    echo '<input type="submit" name="action" value="preview">';
    echo '<input type="submit" name="action" value="save">';
    echo '</form>';
    $watermeterReader = new Reader(true, $config);
    $value = $watermeterReader->read();
    $watermeterReader->writeDebugImage('tmp/input_debug.jpg');
    echo '<img src="tmp/input_debug.jpg" style="float: left;"/>';
    ?>
</body>
</html>
