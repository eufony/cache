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

use Eufony\Cache\Utils\CacheTrait;
use Eufony\Marshaller\MarshallerInterface;
use Eufony\Marshaller\SerializeMarshaller;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Provides an abstract caching implementation that other implementations can
 * inherit from.
 *
 * Accepts a marshaller implementation that is used to prepare cache values for
 * storage.
 *
 * Additionally provides generic implementations of some methods of
 * `CacheItemPoolInterface`.
 * Inheriting classes may overload these for a more optimized implementation
 * specific to the caching backend.
 */
abstract class AbstractCache implements CacheItemPoolInterface
{
    use CacheTrait;

    /**
     * The marshaller implementation used to prepare the cache values.
     *
     * Defaults to an instance of `\Eufony\Marshaller\SerializeMarshaller`.
     *
     * @var \Eufony\Marshaller\MarshallerInterface $marshaller
     */
    protected MarshallerInterface $marshaller;

    /**
     * Class constructor.
     * Creates a new cache pool.
     *
     * Optionally accepts a marshaller implementation.
     * If no marshaller is given, defaults to a `SerializeMarshaller`.
     *
     * @param \Eufony\Marshaller\MarshallerInterface|null $marshaller
     */
    public function __construct(?MarshallerInterface $marshaller = null)
    {
        $this->marshaller = $marshaller ?? new SerializeMarshaller();
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
     * Getter for the marshaller.
     *
     * Returns the current marshaller.
     *
     * @return \Eufony\Marshaller\MarshallerInterface
     */
    public function marshaller(): MarshallerInterface
    {
        return $this->marshaller;
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
        $item = $this->getItem($key);
        return $item->isHit();
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
