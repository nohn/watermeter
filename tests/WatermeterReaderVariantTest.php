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

use nohn\Watermeter\Reader;
use PHPUnit\Framework\TestCase;

class WatermeterReaderVariantTest extends TestCase
{
    private $variants = array(
        'demo' => array(
            'lastValue' => 819.7668,
            'expectedValue' => 819.7797,
            'hasErrors' => false,
            'expectedErrors' => array(),
            'config' => array(
                'logging' => false,
                'maxThreshold' => 0.1,
                'sourceImage' => __DIR__ . '/../public/demo/demo.jpg',
                'sourceImageBrightness' => false,
                'sourceImageContrast' => false,
                'sourceImageEqualize' => false,
                'postprocessing' => true,
                'digitalDigits' => array(
                    '1' => array('x' => 222, 'y' => 373, 'width' => 36, 'height' => 58),
                    '2' => array('x' => 280, 'y' => 373, 'width' => 36, 'height' => 58),
                    '3' => array('x' => 335, 'y' => 373, 'width' => 36, 'height' => 58),
                    '4' => array('x' => 390, 'y' => 373, 'width' => 36, 'height' => 58),
                    '5' => array('x' => 443, 'y' => 373, 'width' => 36, 'height' => 58),
                ),
                'analogGauges' => array(
                    '1' => array('x' => 560, 'y' => 468, 'width' => 142, 'height' => 148),
                    '2' => array('x' => 493, 'y' => 628, 'width' => 142, 'height' => 148),
                    '3' => array('x' => 331, 'y' => 695, 'width' => 142, 'height' => 148),
                    '4' => array('x' => 165, 'y' => 626, 'width' => 142, 'height' => 148),
                ),
            ),
        ),
        'demo_offset_one_off_pass' => array(
            'lastValue' => 819.6798,
            'expectedValue' => 819.7797,
            'hasErrors' => false,
            'expectedErrors' => array(),
            'config' => array(
                'logging' => false,
                'maxThreshold' => 0.1,
                'sourceImage' => __DIR__ . '/../public/demo/demo.jpg',
                'sourceImageBrightness' => false,
                'sourceImageContrast' => false,
                'sourceImageEqualize' => false,
                'postprocessing' => true,
                'digitalDigits' => array(
                    '1' => array('x' => 222, 'y' => 373, 'width' => 36, 'height' => 58),
                    '2' => array('x' => 280, 'y' => 373, 'width' => 36, 'height' => 58),
                    '3' => array('x' => 335, 'y' => 373, 'width' => 36, 'height' => 58),
                    '4' => array('x' => 390, 'y' => 373, 'width' => 36, 'height' => 58),
                    '5' => array('x' => 443, 'y' => 373, 'width' => 36, 'height' => 58),
                ),
                'analogGauges' => array(
                    '1' => array('x' => 560, 'y' => 468, 'width' => 142, 'height' => 148),
                    '2' => array('x' => 493, 'y' => 628, 'width' => 142, 'height' => 148),
                    '3' => array('x' => 331, 'y' => 695, 'width' => 142, 'height' => 148),
                    '4' => array('x' => 165, 'y' => 626, 'width' => 142, 'height' => 148),
                ),
            ),
        ),
        'demo_offset_one_off_fail' => array(
            'lastValue' => 819.6796,
            'expectedValue' => 819.6796,
            'hasErrors' => true,
            'expectedErrors' => array(
                'getReadout() : is_numeric()' => true,
                'getReadout() : increasing' => true,
                'value' => 819.7797,
                'lastValue' => 819.6796,
                'delta' => 0.10009999999999764,
            ),
            'config' => array(
                'logging' => false,
                'maxThreshold' => 0.1,
                'sourceImage' => __DIR__ . '/../public/demo/demo.jpg',
                'sourceImageBrightness' => false,
                'sourceImageContrast' => false,
                'sourceImageEqualize' => false,
                'postprocessing' => true,
                'digitalDigits' => array(
                    '1' => array('x' => 222, 'y' => 373, 'width' => 36, 'height' => 58),
                    '2' => array('x' => 280, 'y' => 373, 'width' => 36, 'height' => 58),
                    '3' => array('x' => 335, 'y' => 373, 'width' => 36, 'height' => 58),
                    '4' => array('x' => 390, 'y' => 373, 'width' => 36, 'height' => 58),
                    '5' => array('x' => 443, 'y' => 373, 'width' => 36, 'height' => 58),
                ),
                'analogGauges' => array(
                    '1' => array('x' => 560, 'y' => 468, 'width' => 142, 'height' => 148),
                    '2' => array('x' => 493, 'y' => 628, 'width' => 142, 'height' => 148),
                    '3' => array('x' => 331, 'y' => 695, 'width' => 142, 'height' => 148),
                    '4' => array('x' => 165, 'y' => 626, 'width' => 142, 'height' => 148),
                ),
            ),
        ),
        'regular1' =>
            array(
                'lastValue' => 1189.1668,
                'expectedValue' => 1189.2776,
                'hasErrors' => false,
                'expectedErrors' => array(),
                'config' => array(
                    'maxThreshold' => '0.2',
                    'sourceImage' => __DIR__ . '/data/variants/1.jpg',
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
                    'logging' => false,
                    'postprocessing' => false,
                ),
            ),
        'regular1_with_offset' =>
            array(
                'lastValue' => 1189.1668,
                'expectedValue' => 3189.2776,
                'hasErrors' => false,
                'expectedErrors' => array(),
                'config' => array(
                    'maxThreshold' => '0.2',
                    'offsetValue' => 2000,
                    'sourceImage' => __DIR__ . '/data/variants/1.jpg',
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
                    'logging' => false,
                    'postprocessing' => false,
                ),
            ),
        'regular1_with_negative_offset' =>
            array(
                'lastValue' => 1189.1668,
                'expectedValue' => 189.2776,
                'hasErrors' => false,
                'expectedErrors' => array(),
                'config' => array(
                    'maxThreshold' => '0.2',
                    'offsetValue' => -1000,
                    'sourceImage' => __DIR__ . '/data/variants/1.jpg',
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
                    'logging' => false,
                    'postprocessing' => false,
                ),
            ),
        'regular1_decreasing' =>
            array(
                'lastValue' => 1189.2777,
                'expectedValue' => 1189.2777,
                'hasErrors' => true,
                'expectedErrors' => array(
                    'getReadout() : is_numeric()' => true,
                    'getReadout() : increasing' => false,
                    'value' => 1189.2776,
                    'lastValue' => 1189.2777,
                    'delta' => -0.00010000000020227162
                ),
                'config' => array(
                    'maxThreshold' => '0.2',
                    'sourceImage' => __DIR__ . '/data/variants/1.jpg',
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
                    'logging' => false,
                    'postprocessing' => false,
                ),
            ),
        'regular2_ocr_failing' =>
            array(
                'lastValue' => 1189.1668,
                'expectedValue' => 1189.2776,
                'hasErrors' => true,
                'expectedErrors' => array(
                    'readDigits() : !is_numeric()' => 'Could not interpret "". Using last known value 1189'
                ),
                'config' => array(
                    'maxThreshold' => '0.2',
                    'sourceImage' => __DIR__ . '/data/variants/2.jpg',
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
                    'logging' => false,
                    'postprocessing' => false,
                ),
            ),
        'regular2_with_brightness_contrast' =>
            array(
                'lastValue' => 1189.1668,
                'expectedValue' => 1189.2776,
                'hasErrors' => false,
                'expectedErrors' => array(),
                'config' => array(
                    'maxThreshold' => '0.2',
                    'sourceImage' => __DIR__ . '/data/variants/2.jpg',
                    'sourceImageBrightness' => '30',
                    'sourceImageContrast' => '50',
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
                    'logging' => false,
                    'postprocessing' => false,
                ),
            ),
        'regular3_with_full_image_processing' =>
            array(
                'lastValue' => 1189.2668,
                'expectedValue' => 1189.3858,
                'hasErrors' => false,
                'expectedErrors' => array(),
                'config' => array(
                    'maxThreshold' => '0.2',
                    'sourceImage' => __DIR__ . '/data/variants/3.jpg',
                    'sourceImageRotate' => '-3',
                    'sourceImageCropSizeX' => '650',
                    'sourceImageCropSizeY' => '600',
                    'sourceImageCropStartX' => '890',
                    'sourceImageCropStartY' => '1360',
                    'sourceImageBrightness' => '30',
                    'sourceImageContrast' => '50',
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
                    'logging' => false,
                    'postprocessing' => false,
                ),
            ),
        'regular4_with_ocr_passing' =>
            array(
                'lastValue' => 1189.9216,
                'expectedValue' => 1189.9244,
                'hasErrors' => false,
                'expectedErrors' => array(),
                'config' => array(
                    'maxThreshold' => '0.2',
                    'sourceImage' => __DIR__ . '/data/variants/4.jpg',
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
                    'logging' => false,
                    'postprocessing' => false,
                ),
            ),
        'regular5_with_ocr_failing_smaller' =>
            array(
                'lastValue' => 1189.9216,
                'expectedValue' => 1189.9216,
                'hasErrors' => true,
                'expectedErrors' => array(
                    'getReadout() : is_numeric()' => true,
                    'getReadout() : increasing' => false,
                    'value' => 1183.9244,
                    'lastValue' => 1189.9216,
                    'delta' => -5.997199999999793,
                ),
                'config' => array(
                    'maxThreshold' => '0.2',
                    'sourceImage' => __DIR__ . '/data/variants/5.jpg',
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
                    'logging' => false,
                    'postprocessing' => false,
                ),
            ),
        'regular6_with_ocr_failing_larger' =>
            array(
                'lastValue' => 1189.9244,
                'expectedValue' => 1189.9244,
                'hasErrors' => true,
                'expectedErrors' => array(
                    'getReadout() : is_numeric()' => true,
                    'getReadout() : increasing' => true,
                    'value' => 41189.9249,
                    'lastValue' => 1189.9244,
                    'delta' => 40000.000499999995,
                ),
                'config' => array(
                    'maxThreshold' => '0.2',
                    'sourceImage' => __DIR__ . '/data/variants/6.jpg',
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
                    'logging' => false,
                    'postprocessing' => false,
                ),
            ),
        'regular7_with_ocr_completely_failing' =>
            array(
                'lastValue' => 1189.9383,
                'expectedValue' => 1189.9594,
                'hasErrors' => true,
                'expectedErrors' => array(
                    'readDigits() : !is_numeric()' => 'Could not interpret "". Using last known value 1189'
                ),
                'config' => array(
                    'maxThreshold' => '0.2',
                    'sourceImage' => __DIR__ . '/data/variants/7.jpg',
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
                    'logging' => false,
                    'postprocessing' => false,
                ),
            ),
        'regular8_post_decimal_colored_digits' =>
            array(
                'lastValue' => 206.9227,
                'expectedValue' => 206.9228,
                'hasErrors' => false,
                'expectedErrors' => array(),
                'config' => array(
                    'maxThreshold' => '0.002',
                    'sourceImage' => __DIR__ . '/data/variants/8-decimal-digits.jpg',
                    'sourceImageRotate' => '1',
                    'sourceImageCropSizeX' => '750',
                    'sourceImageCropSizeY' => '550',
                    'sourceImageCropStartX' => '800',
                    'sourceImageCropStartY' => '400',
                    'sourceImageBrightness' => '30',
                    'sourceImageContrast' => '50',
                    'logging' => false,
                    'postprocessing' => false,
                    'digitDecolorization' => true,
                    'digitalDigits' =>
                        array(
                            1 =>
                                array(
                                    'x' => '30',
                                    'y' => '85',
                                    'width' => '40',
                                    'height' => '80',
                                ),
                            2 =>
                                array(
                                    'x' => '95',
                                    'y' => '85',
                                    'width' => '40',
                                    'height' => '80',
                                ),
                            3 =>
                                array(
                                    'x' => '160',
                                    'y' => '85',
                                    'width' => '40',
                                    'height' => '80',
                                ),
                            4 =>
                                array(
                                    'x' => '230',
                                    'y' => '85',
                                    'width' => '40',
                                    'height' => '80',
                                ),
                            5 =>
                                array(
                                    'x' => '300',
                                    'y' => '85',
                                    'width' => '40',
                                    'height' => '80',
                                ),
                        ),
                    'analogGauges' =>
                        array(
                            1 =>
                                array(
                                    'x' => '490',
                                    'y' => '300',
                                    'width' => '215',
                                    'height' => '205',
                                ),
                        ),
                    'postDecimalDigits' =>
                        array(
                            1 =>
                                array(
                                    'x' => '375',
                                    'y' => '85',
                                    'width' => '40',
                                    'height' => '80',
                                ),
                            2 =>
                                array(
                                    'x' => '445',
                                    'y' => '85',
                                    'width' => '40',
                                    'height' => '80',
                                ),
                            3 =>
                                array(
                                    'x' => '520',
                                    'y' => '85',
                                    'width' => '40',
                                    'height' => '80',
                                ),
                        ),
                ),
            ),
    );

    public function testVariants(): void
    {
        foreach ($this->variants as $variant_id => $variant) {
            $reader = new Reader(false, $variant['config'], $variant['lastValue']);
            $this->assertEqualsWithDelta($variant['expectedValue'], $reader->getValue(), 0.00001, 'Variant ' . $variant_id);
            $this->assertEquals($variant['hasErrors'], $reader->hasErrors(), 'Variant ' . $variant_id);
            $this->assertEquals($variant['expectedErrors'], $reader->getErrors(), 'Variant ' . $variant_id);
        }
    }
}
