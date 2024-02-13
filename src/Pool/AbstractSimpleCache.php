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

use Eufony\Cache\Utils\SimpleCacheTrait;
use Eufony\Marshaller\MarshallerInterface;
use Eufony\Marshaller\SerializeMarshaller;
use Psr\SimpleCache\CacheInterface;

/**
 * Provides an abstract caching implementation that other implementations can
 * inherit from.
 *
 * Accepts a marshaller implementation that is used to prepare cache values for
 * storage.
 *
 * Additionally provides generic implementations of some methods of
 * `CacheInterface`.
 * Inheriting classes may overload these for a more optimized implementation
 * specific to the caching backend.
 */
abstract class AbstractSimpleCache implements CacheInterface
{
    use SimpleCacheTrait;

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
    public function getMultiple($keys, $default = null): iterable
    {
        $keys = $this->psr16_validateIterable($keys);
        $keys = array_map(fn($key) => $this->psr16_validateKey($key), $keys);
        return array_combine($keys, array_map(fn($key) => $this->get($key, $default), $keys));
    }

    /**
     * @inheritDoc
     */
    public function setMultiple($values, $ttl = null): bool
    {
        $values = $this->psr16_validateIterable($values);
        $keys = array_map(fn($key) => $this->psr16_validateKey($key), array_keys($values));
        return !in_array(false, array_map(fn($key, $value) => $this->set($key, $value, $ttl), $keys, $values));
    }

    /**
     * @inheritDoc
     */
    public function deleteMultiple($keys): bool
    {
        $keys = $this->psr16_validateIterable($keys);
        $keys = array_map(fn($key) => $this->psr16_validateKey($key), $keys);
        return !in_array(false, array_map(fn($key) => $this->delete($key), $keys));
    }

    /**
     * @inheritDoc
     */
    public function has($key): bool
    {
        $key = $this->psr16_validateKey($key);
        return $this->get($key) !== null;
    }
}
