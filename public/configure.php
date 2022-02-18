<?php
require __DIR__ . '/../vendor/autoload.php';

use nohn\Watermeter\Cache;
use nohn\Watermeter\Reader;

$watermeterCache = new Cache();
$lastValue = $watermeterCache->getValue();

$fields = array('x', 'y', 'width', 'height');
if (isset($_POST['sourceImage'])) {
    $config['sourceImage'] = $_POST['sourceImage'];
}
if (isset($_POST['maxThreshold'])) {
    $config['maxThreshold'] = $_POST['maxThreshold'];
}
if (isset($_POST['postprocessing']) && ($_POST['postprocessing'] == 'on') || !isset($config['postprocessing'])) {
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
    $newConfig = var_export($config, true);
    file_put_contents('../src/config/config.php', "<?php\n\$config = " . $newConfig . ";");
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
    $watermeterReader = new Reader();
    $value = $watermeterReader->read(true);
    $watermeterReader->writeDebugImage('tmp/input_debug.jpg');
    echo '<img src="tmp/input_debug.jpg" style="float: left;"/>';
    ?>
</body>
</html>
