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
use Eufony\Cache\Marshaller\SerializeMarshaller;
use Eufony\Cache\Utils\CacheTrait;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Provides an abstract caching implementation that other implementations can
 * inherit from.
 *
 * Accepts a marshaller implementation that is used by a `CacheItem` to prepare
 * cache values for storage.
 */
abstract class AbstractCache implements CacheItemPoolInterface
{
    use CacheTrait;

    /**
     * The marshaller implementation used to prepare the cache values.
     *
     * Defaults to an instance of `SerializeMarshaller`.
     *
     * @var \Eufony\Cache\Marshaller\MarshallerInterface $marshaller
     */
    protected MarshallerInterface $marshaller;

    /**
     * Class constructor.
     * Creates a new cache pool.
     *
     * Optionally accepts a marshaller implementation.
     * If no marshaller is given, defaults to a `SerializeMarshaller`.
     *
     * @param \Eufony\Cache\Marshaller\MarshallerInterface|null $marshaller
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
     * @return \Eufony\Cache\Marshaller\MarshallerInterface
     */
    public function marshaller(): MarshallerInterface
    {
        return $this->marshaller;
    }
}
