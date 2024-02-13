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

use Eufony\Cache\CacheItem;
use Eufony\Marshaller\MarshallerInterface;
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
     * The PHP array used to store marshalled nested arrays containing the cache
     * values and expirations.
     *
     * @var string[] $items
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

        // Check if cache miss
        if (!array_key_exists($key, $this->items)) {
            return new CacheItem($key, false);
        }

        // Unmarshall the cached value
        $item = $this->marshaller->unmarshall($this->items[$key]);

        // Delete expired items
        if ($item['expiration'] !== null && $item['expiration'] <= time()) {
            $this->deleteItem($key);
            return new CacheItem($key, false);
        }

        return new CacheItem($key, true, $item['value']);
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
    public function save(CacheItemInterface $item): bool
    {
        // Marshall the value before storage
        $stored = ["value" => $item->value(), "expiration" => $item->expiration()];
        $this->items[$item->getKey()] = $this->marshaller->marshall($stored);
        return true;
    }
}
