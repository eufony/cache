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

use Eufony\Cache\Marshaller\MarshallerInterface;
use Psr\Cache\CacheItemInterface;

/**
 * Provides a caching implementation using a PHP array.
 *
 * The cache items are stored in-memory on a per-process basis and will be
 * cleared as soon as the PHP process finishes.
 */
class ArrayCache extends AbstractCache
{
    /**
     * The PHP array used to store the cache items.
     *
     * @var \Eufony\Cache\CacheItem[] $items
     */
    protected array $items;

    /**
     * @inheritDoc
     */
    public function __construct(?MarshallerInterface $marshaller = null)
    {
        parent::__construct($marshaller);
        $this->items = [];
    }

    /**
     * @inheritDoc
     */
    public function getItem($key): CacheItemInterface
    {
        $key = $this->psr6_validateKey($key);

        if ($this->hasItem($key)) {
            // Return a clone of the original item
            return clone $this->items[$key];
        }

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

        if (!array_key_exists($key, $this->items)) {
            return false;
        }

        // Delete expired items
        if ($this->items[$key]->expired()) {
            $this->deleteItem($key);
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function clear(): bool
    {
        $this->items = [];
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteItem($key): bool
    {
        $key = $this->psr6_validateKey($key);
        unset($this->items[$key]);
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteItems(array $keys): bool
    {
        return !in_array(false, array_map(fn($key) => $this->deleteItem($key), $keys));
    }

    /**
     * @inheritDoc
     */
    public function save(CacheItemInterface $item): bool
    {
        $this->items[$item->getKey()] = $item;
        return true;
    }

    /**
     * @inheritDoc
     */
    public function saveDeferred(CacheItemInterface $item): bool
    {
        return $this->save($item);
    }

    /**
     * @inheritDoc
     */
    public function commit(): bool
    {
        return true;
    }
}
