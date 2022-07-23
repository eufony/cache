<?php
/*
 * Eufony Cache Utilities
 * Copyright (c) 2021 Alpin Gencer
 *
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

use Psr\Cache\CacheItemPoolInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * Provides a wrapper class to adapt a PSR-6 caching implementation to the
 * PSR-16 standards.
 */
class SimpleCacheAdapter implements CacheInterface
{
    use SimpleCacheTrait;

    /**
     * The PSR-6 cache used internally to provide the real caching implementation.
     *
     * @var \Psr\Cache\CacheItemPoolInterface $cache
     */
    protected CacheItemPoolInterface $cache;

    /**
     * Class constructor.
     *
     * Wraps a PSR-6 cache to adapt it to the PSR-16 standards.
     *
     * @param \Psr\Cache\CacheItemPoolInterface $cache
     */
    public function __construct(CacheItemPoolInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Returns the internal PSR-6 cache.
     *
     * @return CacheItemPoolInterface
     */
    public function cache(): CacheItemPoolInterface
    {
        return $this->cache;
    }

    /**
     * @inheritDoc
     */
    public function get($key, $default = null): mixed
    {
        $key = $this->psr16_validateKey($key);

        $item = $this->cache->getItem($key);
        return $item->isHit() ? $item->get() : $default;
    }

    /**
     * @inheritDoc
     */
    public function set($key, $value, $ttl = null): bool
    {
        $key = $this->psr16_validateKey($key);

        $item = $this->cache->getItem($key);
        $item->set($value)->expiresAfter($ttl);

        return $this->cache->save($item);
    }

    /**
     * @inheritDoc
     */
    public function delete($key): bool
    {
        $key = $this->psr16_validateKey($key);
        return $this->cache->deleteItem($key);
    }

    /**
     * @inheritDoc
     */
    public function clear(): bool
    {
        return $this->cache->clear();
    }

    /**
     * @inheritDoc
     */
    public function getMultiple($keys, $default = null): iterable
    {
        $keys = $this->psr16_validateIterable($keys);
        $keys = array_map(fn($key) => $this->psr16_validateKey($key), $keys);

        $items = $this->cache->getItems($keys);
        return array_map(fn($item) => $item->isHit() ? $item->get() : $default, (array)$items);
    }

    /**
     * @inheritDoc
     */
    public function setMultiple($values, $ttl = null): bool
    {
        $values = $this->psr16_validateIterable($values);
        $keys = array_map(fn($key) => $this->psr16_validateKey($key), array_keys($values));
        $values = array_combine($keys, (array)array_values($values));

        $items = $this->cache->getItems(array_keys($values));
        $success = true;

        foreach ($values as $key => $value) {
            $items[$key]->set($value)->expiresAfter($ttl);
            $success = $this->cache->saveDeferred($items[$key]) && $success;
        }

        return $this->cache->commit() && $success;
    }

    /**
     * @inheritDoc
     */
    public function deleteMultiple($keys): bool
    {
        $keys = $this->psr16_validateIterable($keys);
        $keys = array_map(fn($key) => $this->psr16_validateKey($key), $keys);
        return $this->cache->deleteItems($keys);
    }

    /**
     * @inheritDoc
     */
    public function has($key): bool
    {
        $key = $this->psr16_validateKey($key);
        return $this->cache->hasItem($key);
    }
}
