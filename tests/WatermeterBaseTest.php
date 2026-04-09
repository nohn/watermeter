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

use nohn\Watermeter\Watermeter;
use nohn\Watermeter\Config;
use PHPUnit\Framework\TestCase;

class WatermeterBaseTest extends TestCase
{
    private $testImagePath;
    private $outputPath;

    protected function setUp(): void
    {
        $this->testImagePath = __DIR__ . '/data/variants/1.jpg';
        $this->outputPath = __DIR__ . '/tmp/test_output.jpg';
        if (!is_dir(__DIR__ . '/tmp')) {
            mkdir(__DIR__ . '/tmp');
        }
    }

    protected function tearDown(): void
    {
        if (file_exists($this->outputPath)) {
            unlink($this->outputPath);
        }
    }

    public function testConstructorWithAllOptions(): void
    {
        $config = array(
            'sourceImage' => $this->testImagePath,
            'sourceImageRotate' => 90,
            'sourceImageCropStartX' => 10,
            'sourceImageCropStartY' => 10,
            'sourceImageCropSizeX' => 100,
            'sourceImageCropSizeY' => 100,
            'sourceImageBrightness' => 10,
            'sourceImageContrast' => 10,
            'sourceImageEqualize' => true,
        );

        $watermeter = new Watermeter(true, $config, 100.5);
        $this->assertInstanceOf(Watermeter::class, $watermeter);
        
        $watermeter->writeSourceImage($this->outputPath);
        $this->assertFileExists($this->outputPath);
    }

    public function testConstructorWithInvalidCrop(): void
    {
        // Image 1.jpg is 1920x1080 (roughly, let's assume large enough)
        // Let's use huge crop values to trigger the bounds check
        $config = array(
            'sourceImage' => $this->testImagePath,
            'sourceImageCropStartX' => 10000,
            'sourceImageCropStartY' => 10000,
            'sourceImageCropSizeX' => 100,
            'sourceImageCropSizeY' => 100,
        );

        $watermeter = new Watermeter(false, $config);
        $this->assertInstanceOf(Watermeter::class, $watermeter);
    }

    public function testDebugDrawing(): void
    {
        $config = array(
            'sourceImage' => $this->testImagePath,
        );
        $watermeter = new Watermeter(true, $config);
        
        $gauge = ['x' => 10, 'y' => 10, 'width' => 50, 'height' => 50];
        $watermeter->drawDebugImageGauge($gauge);
        
        $digit = ['x' => 70, 'y' => 10, 'width' => 20, 'height' => 30];
        $watermeter->drawDebugImageDigit($digit);
        
        $watermeter->writeDebugImage($this->outputPath);
        $this->assertFileExists($this->outputPath);
    }

    public function testConstructorWithDefaultConfigAndLastValue(): void
    {
        // This will use Config() and Cache() internally
        $watermeter = new Watermeter();
        $this->assertInstanceOf(Watermeter::class, $watermeter);
    }
}
