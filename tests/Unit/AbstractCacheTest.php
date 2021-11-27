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

use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Provides an abstract PSR-6 implementation tester.
 */
abstract class AbstractCacheTest extends TestCase
{
    /**
     * The PSR-6 cache implementation to test.
     *
     * @var \Psr\Cache\CacheItemPoolInterface $cache
     */
    protected CacheItemPoolInterface $cache;

    /**
     * Returns a new instance of a PSR-6 cache implementation to test.
     *
     * @return \Psr\Cache\CacheItemPoolInterface
     */
    abstract public function getCache(): CacheItemPoolInterface;
}
