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

namespace Eufony\Cache\Tests\Unit\Pool;

use Cache\IntegrationTests\SimpleCacheTest;
use Eufony\Cache\Adapter\Psr16Adapter;
use Eufony\Cache\Pool\AbstractSimpleCache;
use Eufony\Cache\Pool\ApcuCache;
use Psr\SimpleCache\CacheInterface;

/**
 * PSR-16 integration tests for `\Eufony\Cache\Pool\AbstractSimpleCache`.
 */
class AbstractSimpleCacheTest extends SimpleCacheTest
{
    /**
     * @inheritDoc
     */
    protected $skippedTests = [
        "testBasicUsageWithLongKey" => "Keys longer than 64 characters are unsupported.",
    ];

    /**
     * @inheritDoc
     */
    public function createSimpleCache(): CacheInterface
    {
        return new class() extends AbstractSimpleCache {
            /**
             * The PSR-16 cache used internally to provide the real caching implementation.
             *
             * @var \Psr\SimpleCache\CacheInterface $cache
             */
            protected CacheInterface $cache;

            /**
             * Class constructor. Creates an anonymous `CacheInterface`
             * implementation to test `AbstractSimpleCache`.
             */
            public function __construct()
            {
                parent::__construct();
                $this->cache = new Psr16Adapter(new ApcuCache());
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
        };
    }
}
