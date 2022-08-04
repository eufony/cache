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

namespace Eufony\Cache;

use DateInterval;
use DateTime;
use DateTimeInterface;
use Psr\Cache\CacheItemInterface;

/**
 * Provides an implementation of the PSR-6 cache item interface.
 */
class CacheItem implements CacheItemInterface
{
    /**
     * Stores the cache key.
     *
     * @var string $key
     */
    protected string $key;

    /**
     * Stores the cache value.
     *
     * @var mixed $value
     */
    protected mixed $value;

    /**
     * Whether the cache query was a hit.
     *
     * @var bool $isHit
     */
    protected bool $isHit;

    /**
     * The UNIX timestamp of the expiration of the cache item, or null if the
     * item does not expire.
     *
     * @var int|null $expiration
     */
    protected int|null $expiration;

    /**
     * Class constructor.
     * Requires the cache key, whether the cache item is a hit, and, if it is a
     * hit, the cache value.
     *
     * @param string $key
     * @param bool $isHit
     * @param mixed|null $value
     *
     * @internal This object SHOULD NOT be instantiated other than by the library.
     */
    public function __construct(string $key, bool $isHit, mixed $value = null)
    {
        $this->key = $key;
        $this->isHit = $isHit;

        if ($isHit) {
            $this->value = $value;
        }

        $this->expiration = null;
    }

    /**
     * Returns the cache value, similar to `get()`.
     *
     * Unlike `get()` returns the value even if the item is not a hit.
     *
     * @return mixed
     */
    public function value(): mixed
    {
        return $this->value ?? null;
    }

    /**
     * Returns the UNIX timestamp of when the cache item expires, or null if the
     * item does not expire.
     *
     * @return int|null
     */
    public function expiration(): int|null
    {
        return $this->expiration;
    }

    /**
     * @inheritDoc
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @inheritDoc
     */
    public function get(): mixed
    {
        if (!$this->isHit) {
            return null;
        }

        return $this->value;
    }

    /**
     * @inheritDoc
     */
    public function isHit(): bool
    {
        return $this->isHit;
    }

    /**
     * @inheritDoc
     */
    public function set($value): static
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function expiresAt($expiration): static
    {
        if ($expiration === null) {
            $expiration = null;
        } elseif ($expiration instanceof DateTimeInterface) {
            $expiration = $expiration->getTimestamp();
        } else {
            throw new InvalidArgumentException("Expiration must be a DateTime or DateTimeImmutable");
        }

        $this->expiration = $expiration;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function expiresAfter($time): static
    {
        if ($time === null) {
            $expiration = null;
        } elseif ($time instanceof DateInterval) {
            $expiration = (new DateTime("now"))->add($time)->getTimeStamp();
        } elseif (is_int($time)) {
            $expiration = time() + $time;
        } else {
            throw new InvalidArgumentException("TTL must be a DateInterval or an int");
        }

        $this->expiration = $expiration;
        return $this;
    }
}
