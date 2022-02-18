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

namespace nohn\Watermeter;

class Config
{
    private $config = array();

    public function __construct()
    {
        require __DIR__ . '/../src/config/config.php';
        $this->config = $config;
    }

    public function get()
    {
        return $this->config;
    }

    public function set($config)
    {
        $this->config = $config;
    }

    public function store()
    {
        $newConfig = var_export($this->config, true);
        file_put_contents(__DIR__ . '/../src/config/config.php', "<?php\n\$config = " . $newConfig . ";");
        return true;
    }
}