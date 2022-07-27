<?php
/*
 * Eufony Cache Utilities
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

namespace Eufony\Cache\Tests;

use DateInterval;
use Eufony\Cache\NullCache;
use Psr\SimpleCache\CacheInterface;

/**
 * PSR-16 unit tests for `\Eufony\Cache\NullCache`.
 */
class NullSimpleCacheTest extends AbstractSimpleCacheTest
{
    /**
     * @inheritDoc
     */
    public function getCache(): CacheInterface
    {
        return new NullCache();
    }

    /**
     * @dataProvider validCacheItems
     */
    public function testSetGet(mixed $value)
    {
        $this->cache->set("foo", $value);
        $this->assertEquals(null, $this->cache->get("foo"));
    }

    /**
     * @depends testSetGet
     */
    public function testSetGetChanged()
    {
        $this->markTestSkipped();
    }

    /**
     * @depends      testSetGet
     * @depends      testSetGetNotFound
     * @dataProvider validTTLs
     * @group slow
     */
    public function testSetExpire(int|DateInterval|null $ttl, bool $expired)
    {
        $this->cache->set("foo", "bar", $ttl);

        // Wait 2 seconds so the cache expires
        sleep(2);

        $this->assertNull($this->cache->get("foo"));
    }

    /**
     * @dataProvider valuesForMultipleMethods
     */
    public function testSetGetMultiple(array $values, $generator, bool $useGenerator)
    {
        $keys = array_keys($values);

        $this->cache->setMultiple($values);
        $result = (array)$this->cache->getMultiple($useGenerator ? $generator($keys) : $keys);

        $this->assertEquals($keys, array_keys($result));

        foreach ($result as $value) {
            $this->assertNull($value);
        }
    }

    /**
     * @depends      testSetGetMultiple
     * @dataProvider validTTLs
     * @group slow
     */
    public function testSetMultipleExpire(int|DateInterval|null $ttl, bool $expired)
    {
        $keys = ["key1", "key2", "key3"];
        $values = array_combine($keys, ["value1", "value2", "value3"]);

        $this->cache->setMultiple($values, $ttl);

        // Wait 2 seconds so the cache expires
        sleep(2);

        $result = $this->cache->getMultiple($keys);

        foreach ($result as $key => $value) {
            $this->assertNull($value);
        }
    }

    /**
     * @depends      testSetGetMultiple
     * @dataProvider valuesForMultipleMethods
     */
    public function testDeleteMultiple(array $values, $generator, bool $useGenerator)
    {
        $keys = array_keys($values);
        $deleted_keys = [$keys[0], $keys[2]];
        $default = "default";

        $this->cache->setMultiple($values);
        $this->cache->deleteMultiple($useGenerator ? $generator($deleted_keys) : $deleted_keys);
        $result = $this->cache->getMultiple($keys, $default);

        foreach ($result as $key => $value) {
            $this->assertEquals($default, $value);
        }
    }

    /**
     * @depends test_setGet
     */
    public function testHas()
    {
        $this->cache->set("foo", "bar");
        $this->assertFalse($this->cache->has("foo"));
        $this->assertFalse($this->cache->has("not-found"));
    }

    /**
     * @depends      testSetGet
     * @dataProvider validTTLs
     * @group slow
     */
    public function testHasExpire(int|DateInterval|null $ttl, bool $expired)
    {
        $this->cache->set("foo", "bar", $ttl);

        // Wait 2 seconds so the cache expires
        sleep(2);

        $this->assertFalse($this->cache->has("foo"));
    }
}
