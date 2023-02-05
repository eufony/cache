<?php
/*
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty ofAdd CacheKeyProvider
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

namespace Eufony\Cache\Adapter;

use Eufony\Cache\CacheItem;
use Eufony\Cache\Utils\CacheTrait;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * Provides a wrapper class to adapt a PSR-16 caching implementation to the
 * PSR-6 standards.
 */
class Psr6Adapter implements CacheItemPoolInterface
{
    use CacheTrait;

    /**
     * The PSR-16 cache used internally to provide the real caching implementation.
     *
     * @var \Psr\SimpleCache\CacheInterface $cache
     */
    protected CacheInterface $cache;

    /**
     * An array used to temporarily store cache values before committing them to
     * the internal cache all at once.
     *
     * @var mixed[] $deferred
     */
    protected array $deferred;

    /**
     * Class constructor.
     * Wraps a PSR-16 cache to adapt it to the PSR-6 standards.
     *
     * @param \Psr\SimpleCache\CacheInterface $cache
     */
    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
        $this->deferred = [];
    }

    /**
     * Class destructor.
     * Commits any deferred cache items still left in the queue.
     */
    public function __destruct()
    {
        $this->commit();
    }

    /**
     * Returns the internal PSR-16 cache.
     *
     * @return \Psr\SimpleCache\CacheInterface
     */
    public function cache(): CacheInterface
    {
        return $this->cache;
    }

    /**
     * @inheritDoc
     */
    public function getItem($key): CacheItemInterface
    {
        $key = $this->psr6_validateKey($key);

        $value = $this->cache->get($key, new CacheItem($key, false));

        if ($value instanceof CacheItem) {
            return new CacheItem($key, array_key_exists($key, $this->deferred), $this->deferred[$key] ?? null);
        }

        return new CacheItem($key, true, $value);
    }

    /**
     * @inheritDoc
     */
    public function getItems(array $keys = []): iterable
    {
        $keys = array_map(fn($key) => $this->psr6_validateKey($key), $keys);

        $values = $this->cache->getMultiple($keys, new CacheItem("", false));
        $items = [];

        foreach (array_combine($keys, (array)$values) as $key => $value) {
            if ($value instanceof CacheItem) {
                if (array_key_exists($key, $this->deferred)) {
                    $items[$key] = new CacheItem($key, true, $this->deferred[$key]);
                } else {
                    $items[$key] = new CacheItem($key, false);
                }
            } else {
                $items[$key] = new CacheItem($key, true, $value);
            }
        }

        return $items;
    }

    /**
     * @inheritDoc
     */
    public function hasItem($key): bool
    {
        $key = $this->psr6_validateKey($key);
        return $this->cache->has($key) || array_key_exists($key, $this->deferred);
    }

    /**
     * @inheritDoc
     */
    public function clear(): bool
    {
        $this->deferred = [];
        return $this->cache->clear();
    }

    /**
     * @inheritDoc
     */
    public function deleteItem($key): bool
    {
        $key = $this->psr6_validateKey($key);
        $success = $this->cache->delete($key);

        if (array_key_exists($key, $this->deferred)) {
            unset($this->deferred[$key]);
            return true;
        }

        return $success;
    }

    /**
     * @inheritDoc
     */
    public function deleteItems(array $keys): bool
    {
        $keys = array_map(fn($key) => $this->psr6_validateKey($key), $keys);
        return $this->cache->deleteMultiple($keys);
    }

    /**
     * @inheritDoc
     */
    public function save(CacheItemInterface $item): bool
    {
        $ttl = $item->expiration() === null ? null : $item->expiration() - time();
        return $this->cache->set($item->getKey(), $item->value(), $ttl);
    }

    /**
     * @inheritDoc
     */
    public function saveDeferred(CacheItemInterface $item): bool
    {
        $this->deferred[$item->getKey()] = $item->value();
        return true;
    }

    /**
     * @inheritDoc
     */
    public function commit(): bool
    {
        $success = $this->cache->setMultiple($this->deferred);
        $this->deferred = [];
        return $success;
    }
}
