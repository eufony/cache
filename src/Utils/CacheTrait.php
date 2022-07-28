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
use Stringable;

/**
 * Provides common functionality for implementing the PSR-6 standards.
 *
 * All methods are prefixed with `psr6_` to avoid naming collisions.
 */
trait CacheTrait
{
    /**
     * Provides validation for PSR-6 cache keys.
     *
     * Ensures that the cache key passed to the various caching methods is valid
     * according to the PSR-6 standards.
     *
     * Casts cache keys that are instances of `Stringable` to strings.
     * Returns the typecast key for easy processing.
     *
     * Throws an `\Eufony\Cache\InvalidArgumentException` if the cache key is
     * invalid.
     *
     * Example usage:
     * ```
     * $key = $this->psr6_validateKey($key);
     * ```
     *
     * @param string|\Stringable $key
     * @return string
     */
    protected function psr6_validateKey($key): string
    {
        // Ensure key can be typecast to string
        if (!is_string($key) && !($key instanceof Stringable)) {
            throw new InvalidArgumentException("Cache key must be a string");
        }

        // Ensure key is not an empty string
        if ($key === "") {
            throw new InvalidArgumentException("Cache key must not be empty");
        }

        // Ensure key does not contain reserved characters
        if (strpbrk($key, '{}()/\@:') !== false) {
            throw new InvalidArgumentException("Cache key contains reserved characters: {}()/\@:");
        }

        // Ensure key is not longer than 64 characters
        if (strlen($key) > 64) {
            throw new InvalidArgumentException("Cache key must not be longer than 64 characters");
        }

        // Ensure objects are cast to strings
        /** @var string $key */
        $key = "$key";

        // Return result
        return $key;
    }
}
