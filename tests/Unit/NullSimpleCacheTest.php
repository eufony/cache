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

    public function test_setGet()
    {
        $this->cache->set("foo", "bar");
        $this->assertNull($this->cache->get("foo"));
    }

    /**
     * @depends      test_setGet
     * @dataProvider data_ttls
     * @group slow
     */
    public function test_set_expire(int|DateInterval $ttl, bool $expired)
    {
        $this->cache->set("foo", "bar", $ttl);
        $expected = null;

        // Wait 2 seconds so the cache expires
        sleep(2);

        $this->assertEquals($expected, $this->cache->get("foo"));
    }

    public function test_setGetMultiple()
    {
        $keys = ["key1", "key2", "key3"];
        $values = array_combine($keys, ["value1", "value2", "value3"]);

        $this->cache->setMultiple($values);
        $result = (array) $this->cache->getMultiple($keys);

        $this->assertEquals($keys, array_keys($result));

        foreach ($result as $value) {
            $this->assertNull($value);
        }
    }

    public function test_setGetMultiple_generator()
    {
        $keys = ["key1", "key2", "key3"];
        $values = array_combine($keys, ["value1", "value2", "value3"]);

        $generator = function ($array) {
            foreach ($array as $key => $value) {
                yield $key => $value;
            }
        };

        $this->cache->setMultiple($generator($values));
        $result = (array) $this->cache->getMultiple($generator($keys));

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
    public function test_setMultiple_expire(int|DateInterval $ttl, bool $expired)
    {
        $keys = ["key1", "key2", "key3"];
        $values = array_combine($keys, ["value1", "value2", "value3"]);

        $this->cache->setMultiple($values, $ttl);

        // Wait 2 seconds so the cache expires
        sleep(2);

        $result = $this->cache->getMultiple($keys);

        foreach ($result as $value) {
            $this->assertNull($value);
        }
    }

    /**
     * @depends test_setGetMultiple
     */
    public function test_deleteMultiple()
    {
        $keys = ["key1", "key2", "key3"];
        $values = array_combine($keys, ["value1", "value2", "value3"]);

        $this->cache->setMultiple($values);
        $this->cache->deleteMultiple(["key1", "key3"]);
        $result = $this->cache->getMultiple($keys, "tea");

        foreach ($result as $value) {
            $this->assertEquals("tea", $value);
        }
    }

    /**
     * @depends test_setGetMultiple
     */
    public function test_deleteMultiple_generator()
    {
        $keys = ["key1", "key2", "key3"];
        $values = array_combine($keys, ["value1", "value2", "value3"]);

        $generator = function () {
            yield "key1";
            yield "key3";
        };

        $this->cache->setMultiple($values);
        $this->cache->deleteMultiple($generator());
        $result = $this->cache->getMultiple(array_keys($values), "tea");

        foreach ($result as $value) {
            $this->assertEquals("tea", $value);
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
    public function test_has_expire(int|DateInterval $ttl, bool $expired)
    {
        $this->cache->set("foo", "bar", $ttl);

        // Wait 2 seconds so the cache expires
        sleep(2);

        $this->assertFalse($this->cache->has("foo"));
    }
}
