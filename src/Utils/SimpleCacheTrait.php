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

use DateInterval;
use DateTime;
use Eufony\Cache\InvalidArgumentException;
use Stringable;

/**
 * Provides common functionality for implementing the PSR-16 standards.
 *
 * All methods are prefixed with `psr16_` to avoid naming collisions.
 */
trait SimpleCacheTrait
{
    /**
     * Provides validation for PSR-16 cache keys.
     *
     * Ensures that the cache key passed to the various caching methods is valid
     * according to the PSR-16 standards.
     *
     * Casts cache keys that are instances of `Stringable` to strings.
     * Returns the typecast key for easy processing.
     *
     * Throws an `\Eufony\Cache\InvalidArgumentException` if the cache key is
     * invalid.
     *
     * Example usage:
     * ```
     * $key = $this->psr16_validateKey($key);
     * ```
     *
     * @param string|\Stringable $key
     * @return string
     */
    protected function psr16_validateKey($key): string
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

    /**
     * Provides validation for PSR-16 TTLs.
     *
     * Ensures that the time-to-live (TTL) passed to the various caching methods is
     * valid according to the PSR-16 standards.
     *
     * Converts the expiration time to its corresponding UNIX timestamp, or null if
     * the cache item doesn't expire.
     * Returns the timestamp for easy processing.
     *
     * Example usage:
     * ```
     * $ttl = $this->psr16_validateTtl($ttl);
     * ```
     *
     * @param int|\DateInterval|null $ttl
     * @return int|null
     */
    protected function psr16_validateTtl($ttl): int|null
    {
        // Check if item expires
        if ($ttl === null) {
            return null;
        }

        // Convert DateIntervals to timestamps
        if ($ttl instanceof DateInterval) {
            return (new DateTime("now"))->add($ttl)->getTimeStamp();
        }

        // Add integers to the current time
        if (is_int($ttl)) {
            return time() + $ttl;
        }

        // Unknown TTL type
        throw new InvalidArgumentException("TTL must be a DateInterval or an int");
    }

    /**
     * Provides validation for PSR-16 iterable arguments.
     *
     * Ensures that the parameter passed to the various "xxxMultiple" caching
     * methods is an iterable value.
     *
     * Casts all iterable parameters to arrays.
     * Returns the typecast iterable for easy processing.
     *
     * Example usage:
     * ```
     * $iterable = $this->psr16_validateIterable($iterable);
     * ```
     *
     * @param $iterable
     * @return array
     */
    public function psr16_validateIterable($iterable): array
    {
        if (!is_iterable($iterable)) {
            throw new InvalidArgumentException("Argument must be an array or Traversable");
        }

        return is_array($iterable) ? $iterable : iterator_to_array($iterable);
    }
}
