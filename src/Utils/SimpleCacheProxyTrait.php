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

use Psr\SimpleCache\CacheInterface;

/**
 * Provides common functionality for wrapping a PSR-16 cache in a proxy class.
 *
 * Inheriting classes must set the `$cache` field in the object constructor.
 */
trait SimpleCacheProxyTrait
{
    /**
     * The PSR-16 cache used internally to provide the real caching implementation.
     *
     * @var \Psr\SimpleCache\CacheInterface $cache
     */
    protected CacheInterface $cache;

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
    public function get($key, $default = null): mixed
    {
        return $this->cache->get($key, $default);
    }

    /**
     * @inheritDoc
     */
    public function set($key, $value, $ttl = null): bool
    {
        return $this->cache->set($key, $value, $ttl);
    }

    /**
     * @inheritDoc
     */
    public function delete($key): bool
    {
        return $this->cache->delete($key);
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
        return $this->cache->get($keys, $default);
    }

    /**
     * @inheritDoc
     */
    public function setMultiple($values, $ttl = null): bool
    {
        return $this->cache->setMultiple($values, $ttl);
    }

    /**
     * @inheritDoc
     */
    public function deleteMultiple($keys): bool
    {
        return $this->cache->deleteMultiple($keys);
    }

    /**
     * @inheritDoc
     */
    public function has($key): bool
    {
        return $this->cache->has($key);
    }
}
