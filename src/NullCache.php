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

use Psr\Cache\CacheItemInterface;

/**
 * Provides a caching implementation based on the Null Object Pattern.
 *
 * The caching method parameters go through the same validation as other
 * implementations, but nothing is actually cached.
 */
class NullCache extends AbstractCache
{
    /**
     * Class constructor.
     * Creates a new cache pool.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    public function getItem($key): CacheItemInterface
    {
        $key = $this->psr6_validateKey($key);
        return new CacheItem($this, $key);
    }

    /**
     * @inheritDoc
     */
    public function getItems(array $keys = []): iterable
    {
        return array_combine($keys, array_map(fn($key) => $this->getItem($key), $keys));
    }

    /**
     * @inheritDoc
     */
    public function hasItem($key): bool
    {
        $key = $this->psr6_validateKey($key);
        return false;
    }

    /**
     * @inheritDoc
     */
    public function clear(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteItem($key): bool
    {
        $key = $this->psr6_validateKey($key);
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteItems(array $keys): bool
    {
        $keys = array_map(fn($key) => $this->deleteItem($key), $keys);
        return true;
    }

    /**
     * @inheritDoc
     */
    public function save(CacheItemInterface $item): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function saveDeferred(CacheItemInterface $item): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function commit(): bool
    {
        return false;
    }
}
