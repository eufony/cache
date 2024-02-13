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

namespace Eufony\Cache\Pool;

use Eufony\Cache\CacheException;
use Eufony\Cache\CacheItem;
use Eufony\Marshaller\MarshallerInterface;
use Psr\Cache\CacheItemInterface;

/**
 * Provides a caching implementation using the PHP APCu cache.
 *
 * The cache items are stored in memory on a multi-process per-host basis.
 * Requires the `apcu` extension.
 */
class ApcuCache extends AbstractCache
{
    /**
     * @inheritDoc
     */
    public function __construct(?MarshallerInterface $marshaller = null)
    {
        // Check if APCu is enabled
        if (function_exists("apcu_enabled") && !apcu_enabled()) {
            throw new CacheException("The PHP APCu extension is not enabled.");
        }

        parent::__construct($marshaller);
    }

    /**
     * @inheritDoc
     */
    public function getItem($key): CacheItemInterface
    {
        $key = $this->psr6_validateKey($key);

        // Check if cache miss
        if (!apcu_exists($key)) {
            return new CacheItem($key, false);
        }

        // Unmarshall the cached value
        $value = $this->marshaller->unmarshall(apcu_fetch($key));
        return new CacheItem($key, true, $value);
    }

    /**
     * @inheritDoc
     */
    public function hasItem($key): bool
    {
        $key = $this->psr6_validateKey($key);
        return apcu_exists($key);
    }

    /**
     * @inheritDoc
     */
    public function clear(): bool
    {
        return apcu_clear_cache();
    }

    /**
     * @inheritDoc
     */
    public function deleteItem($key): bool
    {
        $key = $this->psr6_validateKey($key);

        // apcu_delete() returns false if item does not exist
        // PSR-6 wants true to be returned
        if (!$this->hasItem($key)) {
            return true;
        }

        return apcu_delete($key);
    }

    /**
     * @inheritDoc
     */
    public function save(CacheItemInterface $item): bool
    {
        // Marshall the value before storage
        $value = $this->marshaller->marshall($item->value());
        $ttl = $item->expiration() === null ? 0 : $item->expiration() - time() - 1;
        return apcu_store($item->getKey(), $value, $ttl);
    }
}
