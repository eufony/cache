<?php
/*
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

namespace Eufony\Cache\Utils;

use Eufony\Cache\InvalidArgumentException;
use Exception;

/**
 * Provides methods for getting valid PSR-6 and PSR-16 cache keys from an
 * invalid value or object.
 */
class CacheKeyProvider
{
    /**
     * Private class constructor.
     * This class is not meant to be instantiated.
     */
    private function __construct()
    {
    }

    /**
     * Maps the given value or object to a 64-character alphanumeric string.
     *
     * This method is deterministic:
     * It always returns the same output for the same input.
     *
     * @param mixed $value
     * @return string
     */
    public static function get(mixed $value): string
    {
        try {
            $value = serialize($value);
        } catch (Exception) {
            throw new InvalidArgumentException("Value must be serializable");
        }

        return hash("sha256", $value);
    }
}
