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
use Eufony\Cache\Pool\AbstractCache;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Provides an implementation of the PSR-6 cache item interface.
 */
class CacheItem implements CacheItemInterface
{
    /**
     * The parent cache pool that this cache item belongs to.
     *
     * @var \Psr\Cache\CacheItemPoolInterface $cache
     */
    protected CacheItemPoolInterface $cache;

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
     * Requires the parent cache pool and the cache key.
     *
     * @param \Eufony\Cache\Pool\AbstractCache $cache
     * @param string $key
     */
    public function __construct(AbstractCache $cache, string $key)
    {
        $this->cache = $cache;
        $this->key = $key;
        $this->isHit = false;
        $this->expiration = null;
    }

    /*
     * Magic method for cloning the object.
     * Ensures that cloning a cache item creates a deep copy of all of its
     * properties.
     */
    public function __clone(): void
    {
        unserialize(serialize($this));
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

        // Unmarshall the value using the cache pool's marshaller
        return $this->cache->marshaller()->unmarshall($this->value);
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
        // Marshall the value using the cache pool's marshaller
        $this->value = $this->cache->marshaller()->marshall($value);
        $this->isHit = true;
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

    /**
     * Returns whether this item has expired.
     *
     * A cache item with an unset (`null`) expiration time will be cached forever.
     *
     * @return bool
     */
    public function expired(): bool
    {
        return $this->expiration !== null && $this->expiration <= time();
    }
}
