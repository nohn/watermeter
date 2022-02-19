<?php
$config = array(
    // Enable logging in case data changes or an error occurs. If you'd like to access the logging data when running
    // in docker(-compose), please mount /usr/src/watermeter/src/log/
    'logging' => false,
    // Maximum volume change to accept between two updates.
    'maxThreshold' => 0.2,
    // Source image. Can either be a path to a file or an URL.
    'sourceImage' => 'https://raw.githubusercontent.com/nohn/watermeter/main/tests/data/variants/3.jpg',
    // Source Image Brightness & Contrast tuning
    'sourceImageRotate' => '-3',
    'sourceImageCropSizeX' => '650',
    'sourceImageCropSizeY' => '600',
    'sourceImageCropStartX' => '890',
    'sourceImageCropStartY' => '1360',
    'sourceImageBrightness' => '30',
    'sourceImageContrast' => '50',
    'postprocessing' => false,
    // Each digital digit is identified by it's x and y coordinates and it's height and width
    'digitalDigits' =>
        array(
            2 =>
                array(
                    'x' => '189',
                    'y' => '47',
                    'width' => '36',
                    'height' => '58',
                ),
            3 =>
                array(
                    'x' => '249',
                    'y' => '47',
                    'width' => '36',
                    'height' => '58',
                ),
            4 =>
                array(
                    'x' => '304',
                    'y' => '47',
                    'width' => '36',
                    'height' => '58',
                ),
            5 =>
                array(
                    'x' => '364',
                    'y' => '47',
                    'width' => '36',
                    'height' => '58',
                ),
        ),
    // Each analog gauge is identified by it's x and y coordinates and it's height and width
    'analogGauges' =>
        array(
            1 =>
                array(
                    'x' => '488',
                    'y' => '146',
                    'width' => '148',
                    'height' => '150',
                ),
            2 =>
                array(
                    'x' => '419',
                    'y' => '314',
                    'width' => '148',
                    'height' => '150',
                ),
            3 =>
                array(
                    'x' => '250',
                    'y' => '384',
                    'width' => '148',
                    'height' => '155',
                ),
            4 =>
                array(
                    'x' => '73',
                    'y' => '310',
                    'width' => '150',
                    'height' => '155',
                ),
        ),
);