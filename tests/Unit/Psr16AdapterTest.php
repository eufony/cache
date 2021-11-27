<?php
/*
 * Testsuite for the Eufony Cache Package
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

namespace Tests\Unit;

use Cache\Adapter\PHPArray\ArrayCachePool;
use Eufony\Cache\Psr16Adapter;
use Psr\Cache\CacheItemPoolInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * Unit tests for `\Eufony\Cache\Psr16Adapter`.
 */
class Psr16AdapterTest extends AbstractSimpleCacheTest {

    /**
     * The internal PSR-6 cache implementation used to test the PSR-16 adapter.
     *
     * @var \Psr\Cache\CacheItemPoolInterface $internalCache
     */
    protected CacheItemPoolInterface $internalCache;

    /** @inheritdoc */
    public function getCache(): CacheInterface {
        $this->internalCache = new ArrayCachePool();
        return new Psr16Adapter($this->internalCache);
    }

    public function testGetInternalCachePool() {
        $this->assertSame($this->internalCache, $this->cache->cache());
    }

}