<?php

use nohn\Watermeter\Reader;
use PHPUnit\Framework\TestCase;

class WatermeterReaderVariantTest extends TestCase
{
    private $variants = array(
        array(
            'lastValue' => 1189.1668,
            'expectedValue' => 1189.2776,
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
        array(
            'lastValue' => 1189.1668,
            'expectedValue' => 1189.2776,
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
        array(
            'lastValue' => 1189.1668,
            'expectedValue' => 1189.2776,
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
        array(
            'lastValue' => 1189.2668,
            'expectedValue' => 1189.3858,
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
    );

    public function testVariants(): void
    {
        foreach ($this->variants as $variant) {
            $reader = new Reader(false, $variant['config'], $variant['lastValue']);
            $this->assertEquals($variant['expectedValue'], $reader->read());
        }
    }
}
