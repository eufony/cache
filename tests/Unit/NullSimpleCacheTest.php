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
     * @dataProvider data_validCacheItems
     */
    public function test_setGet(mixed $value)
    {
        $this->cache->set("foo", $value);
        $this->assertEquals(null, $this->cache->get("foo"));
    }

    /**
     * @depends      test_setGet
     * @depends      test_setGet_notFound
     * @dataProvider data_ttls
     * @group slow
     */
    public function test_set_expire(int|DateInterval|null $ttl, bool $expired)
    {
        $this->cache->set("foo", "bar", $ttl);

        // Wait 2 seconds so the cache expires
        sleep(2);

        $this->assertNull($this->cache->get("foo"));
    }

    /**
     * @dataProvider data_multipleValues
     */
    public function test_setGetMultiple(array $keys, array $values, $generator, bool $useGenerator)
    {
        $this->cache->setMultiple($values);
        $result = (array) $this->cache->getMultiple($useGenerator ? $generator($keys) : $keys);

        $this->assertEquals($keys, array_keys($result));

        foreach ($result as $value) {
            $this->assertNull($value);
        }
    }

    /**
     * @depends      test_setGetMultiple
     * @dataProvider data_ttls
     * @group slow
     */
    public function test_setMultiple_expire(int|DateInterval|null $ttl, bool $expired)
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
     * @depends      test_setGetMultiple
     * @dataProvider data_multipleValues
     */
    public function test_deleteMultiple(array $keys, array $values, $generator, bool $useGenerator)
    {
        $deleted_keys = [$keys[0], $keys[2]];
        $default = "tea";

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
    public function test_has()
    {
        $this->cache->set("foo", "bar");
        $this->assertFalse($this->cache->has("foo"));
        $this->assertFalse($this->cache->has("not-found"));
    }

    /**
     * @depends      test_setGet
     * @dataProvider data_ttls
     * @group slow
     */
    public function test_has_expire(int|DateInterval|null $ttl, bool $expired)
    {
        $this->cache->set("foo", "bar", $ttl);

        // Wait 2 seconds so the cache expires
        sleep(2);

        $this->assertFalse($this->cache->has("foo"));
    }
}
