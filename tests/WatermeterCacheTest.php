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

use nohn\Watermeter\Cache;
use PHPUnit\Framework\TestCase;

class WatermeterCacheTest extends TestCase
{
    public function testCacheRead(): void
    {
        $cacheFile = __DIR__ . '/../src/config/lastValue.txt';
        $backupFile = $cacheFile . '.bak.test';
        copy($cacheFile, $backupFile);
        
        try {
            file_put_contents($cacheFile, "1189.2345");
            $watermeter = new Cache();
            $this->assertEquals("1189.2345", $watermeter->getValue());
            $this->assertIsNumeric($watermeter->getLastUpdate());
            $this->assertGreaterThan(0, $watermeter->getLastUpdate());
        } finally {
            rename($backupFile, $cacheFile);
        }
    }

    public function testCacheUpdate(): void
    {
        $cacheFile = __DIR__ . '/../src/config/lastValue.txt';
        $backupFile = $cacheFile . '.bak.test';
        copy($cacheFile, $backupFile);

        try {
            file_put_contents($cacheFile, "1234.5678");
            touch($cacheFile, time() - 100);

            $cache = new Cache();
            $this->assertEquals("1234.5678", $cache->getValue());
            $this->assertEquals(time() - 100, $cache->getLastUpdate(), '', 2);
        } finally {
            rename($backupFile, $cacheFile);
        }
    }

    public function testCacheNonExistent(): void
    {
        $cacheFile = __DIR__ . '/../src/config/lastValue.txt';
        $backupFile = $cacheFile . '.bak.test';
        copy($cacheFile, $backupFile);
        unlink($cacheFile);

        try {
            $cache = new Cache();
            $this->assertEquals(0, $cache->getValue());
            $this->assertEquals(0, $cache->getLastUpdate());
        } finally {
            rename($backupFile, $cacheFile);
        }
    }
}
