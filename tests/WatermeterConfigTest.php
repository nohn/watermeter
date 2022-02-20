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

use nohn\Watermeter\Config;
use PHPUnit\Framework\TestCase;

class WatermeterConfigTest extends TestCase
{
    public function testConfigRead(): void
    {
        $watermeterConfig = new Config();
        $config = $watermeterConfig->get();
        $this->assertEquals(false, $config['logging']);
        $this->assertEquals(0.2, $config['maxThreshold']);
        $this->assertEquals('https://raw.githubusercontent.com/nohn/watermeter/main/tests/data/variants/3.jpg', $config['sourceImage']);
        $this->assertEquals(array(
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
        ), $config['digitalDigits']);
        $this->assertEquals(array(
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
