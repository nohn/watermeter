<?php
$config = array(
    // Maximum volume change to accept between two updates.
    'maxThreshold' => 0.1,
    // Source image. Can either be a path to a file or an URL.
    'sourceImage' => 'demo/demo.jpg',
    // Each digital digit is identified by it's x and y coordinates and it's height and width
    'digitalDigits' => array(
        '1' => array('x' => 222, 'y' => 373, 'width' => 36, 'height' => 58),
        '2' => array('x' => 280, 'y' => 373, 'width' => 36, 'height' => 58),
        '3' => array('x' => 335, 'y' => 373, 'width' => 36, 'height' => 58),
        '4' => array('x' => 390, 'y' => 373, 'width' => 36, 'height' => 58),
        '5' => array('x' => 443, 'y' => 373, 'width' => 36, 'height' => 58),
    ),
    // Each analog gauge is identified by it's x and y coordinates and it's height and width
    'analogGauges' => array(
        '1' => array('x' => 560, 'y' => 468, 'width' => 142, 'height' => 148),
        '2' => array('x' => 493, 'y' => 628, 'width' => 142, 'height' => 148),
        '3' => array('x' => 331, 'y' => 695, 'width' => 142, 'height' => 148),
        '4' => array('x' => 165, 'y' => 626, 'width' => 142, 'height' => 148),
    ),
);