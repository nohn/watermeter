<?php
/**
 * Watermeter
 *
 * A tool for reading water meters
 *
 * PHP Version 8.5
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
 * @copyright 2026 Sebastian Nohn
 * @license   http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License version 3.0
 */

namespace nohn\Watermeter;

class Cache
{
    private float $value = 0;
    private int $last_update = 0;

    public function __construct()
    {
        $cacheFile = __DIR__ . '/../src/config/lastValue.txt';
        if (file_exists($cacheFile)) {
            $content = file_get_contents($cacheFile);
            if ($content !== false) {
                $this->value = (float)trim($content);
            }
            $mtime = filemtime($cacheFile);
            if ($mtime !== false) {
                $this->last_update = $mtime;
            }
        }
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function getLastUpdate(): int
    {
        return $this->last_update;
    }
}