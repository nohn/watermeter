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

require __DIR__ . '/../vendor/autoload.php';

use nohn\Watermeter\Cache;
use nohn\Watermeter\Config;
use nohn\Watermeter\Reader;

$watermeterCache = new Cache();
$lastValue = $watermeterCache->getValue();

$watermeterConfig = new Config();
$config = $watermeterConfig->get();

$fields = array('x', 'y', 'width', 'height');
if (isset($_POST) && !empty($_POST)) {
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
    if (isset($_POST['allowDecreasing']) && ($_POST['allowDecreasing'] == 'on')) {
        $config['allowDecreasing'] = true;
    } else {
        $config['allowDecreasing'] = false;
    }
    if (isset($_POST['digitDecolorization']) && ($_POST['digitDecolorization'] == 'on')) {
        $config['digitDecolorization'] = true;
    } else {
        $config['digitDecolorization'] = false;
    }
    if (isset($_POST['digitalDigitsInversion']) && ($_POST['digitalDigitsInversion'] == 'on')) {
        $config['digitalDigitsInversion'] = true;
    } else {
        $config['digitalDigitsInversion'] = false;
    }
    if (isset($_POST['lastValue'])) {
        $lastValue = $_POST['lastValue'];
    }
    if (isset($_POST['offsetValue'])) {
        $config['offsetValue'] = $_POST['offsetValue'];
    }
    if (isset($_POST['digit'])) {
        $config['digitalDigits'] = $_POST['digit'];
    }
    if (isset($_POST['postDecimalDigit'])) {
        $config['postDecimalDigits'] = $_POST['postDecimalDigit'];
    } else {
        $config['postDecimalDigits'] = array();
    }
    if (isset($_POST['gauge'])) {
        $config['analogGauges'] = $_POST['gauge'];
    } else {
        $config['analogGauges'] = array();
    }
    if (isset($_POST['action'])) {
        $configDump = var_export($config, true);
        if ($_POST['action'] == 'save') {
            $watermeterConfig->set($config);
            $watermeterConfig->store();
            file_put_contents('../src/config/lastValue.txt', $lastValue);
        }
    }
}

?>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Watermeter - Configure</title>
    <style type="text/css">
        :root {
            --bg: #f6f7fb;
            --text: #111827;
            --muted: #6b7280;
            --card: #ffffff;
            --border: #e5e7eb;
            --primary: #2563eb;
            --primary-600: #1d4ed8;
            --ring: rgba(37, 99, 235, 0.25);
            --danger: #ef4444;
        }

        html, body {
            margin: 0;
            padding: 0;
            background: var(--bg);
            color: var(--text);
            font-family: Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.5;
        }

        /* Layout */
        body {
            padding: 24px;
        }
        .container {
            display: flex;
            flex-wrap: wrap;
            gap: 24px;
            align-items: flex-start;
        }
        #config {
            float: none !important; /* override inline float */
            max-width: 600px;
            flex: 1 1 500px;
            display: grid;
            grid-template-columns: 1fr;
            gap: 16px;
        }
        .preview {
            flex: 1 1 400px;
            position: sticky;
            top: 24px;
        }

        /* Fieldsets as cards */
        fieldset {
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 16px 16px 8px 16px;
            background: var(--card);
            box-shadow: 0 1px 2px rgba(0,0,0,0.04);
        }
        fieldset legend {
            font-weight: 600;
            color: var(--text);
            padding: 0 4px;
        }

        /* Label-like legends placed before inputs */
        .base legend[for],
        .coordinates legend[for] {
            display: block;
            float: none;
            width: auto;
            margin: 12px 0 6px 0;
            color: var(--muted);
            font-size: 0.9rem;
        }

        /* Inputs */
        .base input,
        .coordinates input,
        input[type="text"] {
            float: none;
            width: 100%;
            box-sizing: border-box;
            height: 40px;
            padding: 8px 12px;
            border: 1px solid var(--border);
            border-radius: 10px;
            background: #fff;
            color: var(--text);
            outline: none;
            transition: border-color .15s ease, box-shadow .15s ease;
        }
        input[type="text"]:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px var(--ring);
        }

        /* Checkboxes */
        input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--primary);
            vertical-align: middle;
            margin-right: 8px;
        }
        /* Keep checkbox legends on same row for readability */
        #sourceImageEqualize,
        #postprocessing,
        #allowDecreasing,
        #digitDecolorization,
        #digitalDigitsInversion {
            margin-right: 8px;
        }

        /* Buttons */
        button,
        input[type="submit"] {
            appearance: none;
            border: 1px solid var(--primary);
            background: var(--primary);
            color: #fff;
            font-weight: 600;
            padding: 10px 14px;
            border-radius: 10px;
            cursor: pointer;
            transition: background .15s ease, border-color .15s ease, transform .02s ease;
            margin: 6px 8px 6px 0;
        }
        button:hover,
        input[type="submit"]:hover {
            background: var(--primary-600);
            border-color: var(--primary-600);
        }
        button:active,
        input[type="submit"]:active { transform: translateY(1px); }

        /* Debug/preview image */
        .preview {
            position: relative;
            display: inline-block;
        }
        #selection-canvas {
            position: absolute;
            top: 0;
            left: 0;
            cursor: crosshair;
        }
        img[src*="tmp/input_debug.jpg"] {
            display: block;
            max-width: 100%;
            height: auto;
            border-radius: 12px;
            border: 1px solid var(--border);
            box-shadow: 0 2px 6px rgba(0,0,0,0.07);
        }

        /* Config dump */
        pre {
            background: #0f172a;
            color: #e5e7eb;
            padding: 16px;
            border-radius: 12px;
            overflow: auto;
            border: 1px solid #1f2937;
        }

        /* Compact layout for digit and gauge items */
        .coordinates {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: flex-start;
        }
        .coordinates > legend {
            width: 100%;
            flex: 0 0 100%;
        }
        .coordinates > fieldset {
            flex: 0 1 180px;
            margin-top: 0;
            padding: 10px 10px 8px 10px;
            border-radius: 10px;
        }
        .coordinates > button {
            flex: 0 0 auto;
            align-self: flex-end;
            margin-bottom: 8px;
        }
        .coordinates > fieldset > legend {
            display: inline-block;
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--muted);
            margin-bottom: 6px;
        }
        .coordinates > fieldset legend[for] {
            margin: 8px 0 4px 0;
            font-size: 0.78rem;
            color: var(--muted);
            display: block;
        }
        .coordinates > fieldset legend[for] + input {
            height: 32px;
            padding: 6px 10px;
            border-radius: 8px;
            font-size: 0.9rem;
            display: block;
            width: 100%;
        }
        @media (max-width: 520px) {
            .coordinates > fieldset {
                flex: 1 1 140px;
            }
        }

        /* Make long pages easier to scan */
        .base {
            display: grid;
            grid-template-columns: 1fr 1fr;
            column-gap: 16px;
        }
        .base legend[for] + input { /* pair label and input in grid flow */
            margin-bottom: 8px;
        }
        @media (max-width: 900px) {
            .base { grid-template-columns: 1fr; }
        }
    </style>
    <script type="text/javascript">
        var selection = {x: 0, y: 0, w: 0, h: 0};
        var activeTarget = null;
        var isDrawing = false;
        var startX, startY;

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

        function startSelection(targetPrefix) {
            activeTarget = targetPrefix;
            var canvas = document.getElementById('selection-canvas');
            var img = document.getElementById('preview-image');
            if (!canvas || !img) return;

            // Align canvas exactly with the image's content area (inside borders)
            canvas.width = img.clientWidth;
            canvas.height = img.clientHeight;
            canvas.style.left = (img.offsetLeft + img.clientLeft) + 'px';
            canvas.style.top = (img.offsetTop + img.clientTop) + 'px';
            canvas.style.display = 'block';
        }

        window.onload = function() {
            var canvas = document.getElementById('selection-canvas');
            if (!canvas) return;
            var ctx = canvas.getContext('2d');
            var img = document.getElementById('preview-image');

            canvas.onmousedown = function(e) {
                var rect = canvas.getBoundingClientRect();
                startX = e.clientX - rect.left;
                startY = e.clientY - rect.top;
                isDrawing = true;
            };

            canvas.onmousemove = function(e) {
                if (!isDrawing) return;
                var rect = canvas.getBoundingClientRect();
                var x = e.clientX - rect.left;
                var y = e.clientY - rect.top;
                var w = x - startX;
                var h = y - startY;
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                ctx.strokeStyle = 'red';
                ctx.strokeRect(startX, startY, w, h);
            };

            canvas.onmouseup = function(e) {
                if (!isDrawing) return;
                isDrawing = false;
                var rect = canvas.getBoundingClientRect();
                var x = e.clientX - rect.left;
                var y = e.clientY - rect.top;
                
                var scaleX = img.naturalWidth / img.clientWidth;
                var scaleY = img.naturalHeight / img.clientHeight;

                var realX = Math.round(Math.min(startX, x) * scaleX);
                var realY = Math.round(Math.min(startY, y) * scaleY);
                var realW = Math.round(Math.abs(x - startX) * scaleX);
                var realH = Math.round(Math.abs(y - startY) * scaleY);

                if (activeTarget) {
                    document.getElementsByName(activeTarget + '[x]')[0].value = realX;
                    document.getElementsByName(activeTarget + '[y]')[0].value = realY;
                    document.getElementsByName(activeTarget + '[width]')[0].value = realW;
                    document.getElementsByName(activeTarget + '[height]')[0].value = realH;
                }
                canvas.style.display = 'none';
                ctx.clearRect(0, 0, canvas.width, canvas.height);
            };
        };
    </script>
</head>
<body>
<div class="container">
<form method="post" id="config" style="float: left;">
    <fieldset class="base">
        <legend>Base Settings</legend>
        <legend for="sourceImage">Source Image</legend>
        <input type="text" name="sourceImage" id="sourceImage"
               value="<?php echo isset($config['sourceImage']) ? $config['sourceImage'] : ''; ?>">
        <legend for="sourceImageRotate">Source Image Rotate °</legend>
        <input type="text" name="sourceImageRotate" id="sourceImageRotate"
               value="<?php echo isset($config['sourceImageRotate']) ? $config['sourceImageRotate'] : ''; ?>">
        <legend for="sourceImageCropStartX">Source Image Crop Start x</legend>
        <input type="text" name="sourceImageCropStartX" id="sourceImageCropStartX"
               value="<?php echo isset($config['sourceImageCropStartX']) ? $config['sourceImageCropStartX'] : ''; ?>">
        <legend for="sourceImageCropStartY">Source Image Crop Start y</legend>
        <input type="text" name="sourceImageCropStartY" id="sourceImageCropStartY"
               value="<?php echo isset($config['sourceImageCropStartY']) ? $config['sourceImageCropStartY'] : '' ?>">
        <legend for="sourceImageCropSizeX">Source Image Crop Width</legend>
        <input type="text" name="sourceImageCropSizeX" id="sourceImageCropSizeX"
               value="<?php echo isset($config['sourceImageCropSizeX']) ? $config['sourceImageCropSizeX'] : ''; ?>">
        <legend for="sourceImageCropSizeY">Source Image Crop Height</legend>
        <input type="text" name="sourceImageCropSizeY" id="sourceImageCropSizeY"
               value="<?php echo isset($config['sourceImageCropSizeY']) ? $config['sourceImageCropSizeY'] : ''; ?>">
        <legend for="sourceImageBrightness">Source Image Brightness Adjust (%)</legend>
        <input type="text" name="sourceImageBrightness" id="sourceImageBrightness"
               value="<?php echo isset($config['sourceImageBrightness']) ? $config['sourceImageBrightness'] : ''; ?>">
        <legend for="sourceImageContrast">Source Image Contrast Adjust (%)</legend>
        <input type="text" name="sourceImageContrast" id="sourceImageContrast"
               value="<?php echo isset($config['sourceImageContrast']) ? $config['sourceImageContrast'] : ''; ?>">
        <legend for="sourceImageEqualize">Source Image histogram equalization</legend>
        <input type="checkbox" name="sourceImageEqualize"
               id="sourceImageEqualize" <?php echo (isset($config['sourceImageEqualize']) && $config['sourceImageEqualize'] == true) ? 'checked' : ''; ?>>
        <legend for="maxThreshold">Max. Threshold</legend>
        <input type="text" name="maxThreshold" id="maxThreshold"
               value="<?php echo isset($config['maxThreshold']) ? $config['maxThreshold'] : ''; ?>">
        <legend for="lastValue">Initial Value</legend>
        <input type="text" name="lastValue" id="lastValue" value="<?php echo isset($lastValue) ? $lastValue : ''; ?>">
        <legend for="offsetValue">Offset Value</legend>
        <input type="text" name="offsetValue" id="offsetValue"
               value="<?php echo isset($config['offsetValue']) ? $config['offsetValue'] : ''; ?>">
        <legend for="postprocessing">Digit Postprocessing</legend>
        <input type="checkbox" name="postprocessing"
               id="postprocessing" <?php echo (isset($config['postprocessing']) && $config['postprocessing'] == true) ? 'checked' : ''; ?>>
        <legend for="digitDecolorization">Digit Decolorization</legend>
        <input type="checkbox" name="digitDecolorization"
               id="digitDecolorization" <?php echo (isset($config['digitDecolorization']) && $config['digitDecolorization'] == true) ? 'checked' : ''; ?>>
        <legend for="digitalDigitsInversion">Digit Inversion</legend>
        <input type="checkbox" name="digitalDigitsInversion"
               id="digitalDigitsInversion" <?php echo (isset($config['digitalDigitsInversion']) && $config['digitalDigitsInversion'] == true) ? 'checked' : ''; ?>>
        <legend for="allowDecreasing">Allow Decreasing Values</legend>
        <input type="checkbox" name="allowDecreasing"
               id="allowDecreasing" <?php echo (isset($config['allowDecreasing']) && $config['allowDecreasing'] == true) ? 'checked' : ''; ?>>
    </fieldset>
    <?php
    echo '<fieldset class="coordinates"><legend>Pre Decimal Digital Digits</legend>';
    foreach ($config['digitalDigits'] as $key => $digit) {
        echo '<fieldset id="digit_' . $key . '"><legend>' . $key . ' <button type="button" onclick="startSelection(\'digit[' . $key . ']\')">Select</button></legend>';
        foreach ($fields as $field) {
            echo '<legend for="digit[' . $key . '][' . $field . ']">' . $field . '</legend><input name="digit[' . $key . '][' . $field . ']" id="digit[' . $key . '][' . $field . ']" type="text" value="' . (isset($digit[$field]) ? $digit[$field] : '') . '">';
        }
        echo '</fieldset>';
    }
    echo '<button onclick="return removeElement(\'digit\')" />Remove a Digit</button>';
    echo '<button onclick="return addElement(\'digit\')" />Add a Digit</button>';
    echo '</fieldset>';
    echo '<fieldset class="coordinates"><legend>Post Decimal Digital Digits</legend>';
    if (isset($config['postDecimalDigits'])) {
        foreach ($config['postDecimalDigits'] as $key => $digit) {
            echo '<fieldset id="postDecimalDigit_' . $key . '"><legend>' . $key . ' <button type="button" onclick="startSelection(\'postDecimalDigit[' . $key . ']\')">Select</button></legend>';
            foreach ($fields as $field) {
                echo '<legend for="postDecimalDigit[' . $key . '][' . $field . ']">' . $field . '</legend><input name="postDecimalDigit[' . $key . '][' . $field . ']" id="digit[' . $key . '][' . $field . ']" type="text" value="' . (isset($digit[$field]) ? $digit[$field] : '') . '">';
            }
            echo '</fieldset>';
        }
    }
    echo '<button onclick="return removeElement(\'postDecimalDigit\')" />Remove a Digit</button>';
    echo '<button onclick="return addElement(\'postDecimalDigit\')" />Add a Digit</button>';
    echo '</fieldset>';
    echo '<fieldset class="coordinates"><legend>Analog Digits</legend>';
    foreach ($config['analogGauges'] as $key => $gauge) {
        echo '<fieldset id="gauge_' . $key . '"><legend>' . $key . ' <button type="button" onclick="startSelection(\'gauge[' . $key . ']\')">Select</button></legend>';
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
    echo '<div class="preview">';
    $watermeterReader = new Reader(true, $config);
    $value = $watermeterReader->getReadout();
    $watermeterReader->writeDebugImage('tmp/input_debug.jpg');
    echo '<img src="tmp/input_debug.jpg" id="preview-image" />';
    echo '<canvas id="selection-canvas" style="display:none"></canvas>';
    echo '</div>';
    echo '</div>';
    ?>
    <?php if(isset($configDump)): ?>
        <div style="clear: both;"><pre><?php echo $configDump; ?></pre></div>
    <?php endif; ?>
</body>
</html>
