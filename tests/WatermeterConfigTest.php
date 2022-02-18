<?php

use nohn\Watermeter\Config;
use PHPUnit\Framework\TestCase;

class WatermeterConfigTest extends TestCase
{
    public function testConfigRead(): void
    {
        $watermeterConfig = new Config();
        $config = $watermeterConfig->get();
        $this->assertEquals(false, $config['logging']);
        $this->assertEquals(0.1, $config['maxThreshold']);
        $this->assertEquals('https://raw.githubusercontent.com/nohn/watermeter/main/src/demo/demo.jpg', $config['sourceImage']);
        $this->assertEquals(array(
            '1' => array('x' => 222, 'y' => 373, 'width' => 36, 'height' => 58),
            '2' => array('x' => 280, 'y' => 373, 'width' => 36, 'height' => 58),
            '3' => array('x' => 335, 'y' => 373, 'width' => 36, 'height' => 58),
            '4' => array('x' => 390, 'y' => 373, 'width' => 36, 'height' => 58),
            '5' => array('x' => 443, 'y' => 373, 'width' => 36, 'height' => 58),
        ), $config['digitalDigits']);
        $this->assertEquals(array(
            '1' => array('x' => 560, 'y' => 468, 'width' => 142, 'height' => 148),
            '2' => array('x' => 493, 'y' => 628, 'width' => 142, 'height' => 148),
            '3' => array('x' => 331, 'y' => 695, 'width' => 142, 'height' => 148),
            '4' => array('x' => 165, 'y' => 626, 'width' => 142, 'height' => 148),
        ), $config['analogGauges']);
    }

    public function testConfigSet(): void
    {
        $watermeterConfig = new Config();
        $config = $watermeterConfig->get();
        $config['logging'] = true;
        $config['maxThreshold'] = 0.2;
        $config['sourceImage'] = 'https://www.example.com/example.jpg';
        $config['digitalDigits'] = array(
            '1' => array('x' => 226, 'y' => 673, 'width' => 66, 'height' => 68),
            '2' => array('x' => 286, 'y' => 673, 'width' => 66, 'height' => 68),
            '3' => array('x' => 336, 'y' => 673, 'width' => 66, 'height' => 68),
            '4' => array('x' => 396, 'y' => 673, 'width' => 66, 'height' => 68),
            '5' => array('x' => 446, 'y' => 673, 'width' => 66, 'height' => 68),
        );
        $config['analogGauges'] = array(
            '1' => array('x' => 566, 'y' => 469, 'width' => 148, 'height' => 149),
            '2' => array('x' => 496, 'y' => 629, 'width' => 148, 'height' => 149),
            '3' => array('x' => 336, 'y' => 699, 'width' => 148, 'height' => 149),
            '4' => array('x' => 166, 'y' => 629, 'width' => 148, 'height' => 149),
        );
        $watermeterConfig->set($config);
        $newConfig = $watermeterConfig->get();
        $this->assertEquals(true, $newConfig['logging']);
        $this->assertEquals(0.2, $newConfig['maxThreshold']);
        $this->assertEquals('https://www.example.com/example.jpg', $newConfig['sourceImage']);
        $this->assertEquals(array(
            '1' => array('x' => 226, 'y' => 673, 'width' => 66, 'height' => 68),
            '2' => array('x' => 286, 'y' => 673, 'width' => 66, 'height' => 68),
            '3' => array('x' => 336, 'y' => 673, 'width' => 66, 'height' => 68),
            '4' => array('x' => 396, 'y' => 673, 'width' => 66, 'height' => 68),
            '5' => array('x' => 446, 'y' => 673, 'width' => 66, 'height' => 68),
        ), $newConfig['digitalDigits']);
        $this->assertEquals(array(
            '1' => array('x' => 566, 'y' => 469, 'width' => 148, 'height' => 149),
            '2' => array('x' => 496, 'y' => 629, 'width' => 148, 'height' => 149),
            '3' => array('x' => 336, 'y' => 699, 'width' => 148, 'height' => 149),
            '4' => array('x' => 166, 'y' => 629, 'width' => 148, 'height' => 149),
        ), $newConfig['analogGauges']);
    }
}
