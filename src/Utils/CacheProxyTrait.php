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

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Provides common functionality for wrapping a PSR-6 cache in a proxy class.
 *
 * Inheriting classes must set the `$cache` field in the object constructor.
 */
trait CacheProxyTrait
{
    /**
     * The PSR-6 cache used internally to provide the real caching implementation.
     *
     * @var \Psr\Cache\CacheItemPoolInterface $cache
     */
    protected CacheItemPoolInterface $cache;

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
    public function getItem($key): CacheItemInterface
    {
        return $this->cache->getItem($key);
    }

    /**
     * @inheritDoc
     */
    public function getItems(array $keys = []): iterable
    {
        return $this->cache->getItems($keys);
    }

    /**
     * @inheritDoc
     */
    public function hasItem($key): bool
    {
        return $this->cache->hasItem($key);
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
    public function deleteItem($key): bool
    {
        return $this->cache->deleteItem($key);
    }

    /**
     * @inheritDoc
     */
    public function deleteItems(array $keys): bool
    {
        return $this->cache->deleteItems($keys);
    }

    /**
     * @inheritDoc
     */
    public function save(CacheItemInterface $item): bool
    {
        return $this->cache->save($item);
    }

    /**
     * @inheritDoc
     */
    public function saveDeferred(CacheItemInterface $item): bool
    {
        return $this->cache->saveDeferred($item);
    }

    /**
     * @inheritDoc
     */
    public function commit(): bool
    {
        return $this->cache->commit();
    }
}
