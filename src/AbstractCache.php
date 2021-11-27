<?php
/*
 * The Eufony Cache Package
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

namespace Eufony\Cache;

use Eufony\Cache\Util\CacheTrait;
use Eufony\Cache\Util\SimpleCacheAdapter;
use Psr\Cache\CacheItemPoolInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * Provides an abstract caching implementation.
 *
 * Implements both the PSR-6 and PSR-16 cache interfaces.
 * Inheriting classes only need to implement the PSR-6 interface methods; the
 * PSR-16 methods are implemented using a PSR-6 to PSR-16 adapter class.
 *
 * @see \Eufony\Cache\Util\SimpleCacheAdapter
 */
abstract class AbstractCache implements CacheItemPoolInterface, CacheInterface
{
    use CacheTrait;

    /**
     * The PSR-6 to PSR-16 adapter used to implement the PSR-16 caching
     * methods.
     *
     * @var \Eufony\Cache\Util\SimpleCacheAdapter $adapter
     */
    protected SimpleCacheAdapter $adapter;

    /**
     * Class constructor.
     *
     * Wraps this implementation of a PSR-6 cache in an adapter class to
     * additionally provide a PSR-16 implementation.
     */
    public function __construct()
    {
        $this->adapter = new SimpleCacheAdapter($this);
    }

    /**
     * @inheritDoc
     */
    public function get($key, $default = null): mixed
    {
        return $this->adapter->get($key, $default);
    }

    /**
     * @inheritDoc
     */
    public function set($key, $value, $ttl = null): bool
    {
        return $this->adapter->set($key, $value, $ttl);
    }

    /**
     * @inheritDoc
     */
    public function delete($key): bool
    {
        return $this->adapter->delete($key);
    }

    /**
     * @inheritDoc
     */
    public function clear(): bool
    {
        return $this->adapter->clear();
    }

    /**
     * @inheritDoc
     */
    public function getMultiple($keys, $default = null): iterable
    {
        return $this->adapter->getMultiple($keys, $default);
    }

    /**
     * @inheritDoc
     */
    public function setMultiple($values, $ttl = null): bool
    {
        return $this->adapter->setMultiple($values, $ttl);
    }

    /**
     * @inheritDoc
     */
    public function deleteMultiple($keys): bool
    {
        return $this->adapter->deleteMultiple($keys);
    }

    /**
     * @inheritDoc
     */
    public function has($key): bool
    {
        return $this->adapter->has($key);
    }
}
