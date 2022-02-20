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

class WatermeterReaderTest extends TestCase
{
    /**
     * @covers \nohn\Watermeter\Reader::readDigits
     * @covers \nohn\Watermeter\Reader::readGauges
     * @covers \nohn\Watermeter\Reader::getReadout
     */
    public function testReadout(): void
    {
        $reader = new Reader();
        $this->assertEquals("1189.3858", $reader->getReadout());
    }
}
