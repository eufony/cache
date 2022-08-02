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

namespace Eufony\Cache\Tests\Integration\Adapter;

use Cache\IntegrationTests\CachePoolTest;
use Eufony\Cache\Adapter\TagAwareAdapter;
use Eufony\Cache\Pool\ArrayCache;
use Eufony\Cache\TagAwareInterface;
use Psr\Cache\CacheItemPoolInterface;

/**
 * PSR-6 integration tests for `\Eufony\Cache\Adapter\TagAwareAdapter`.
 */
class TagAwareAdapterTest extends CachePoolTest
{
    /**
     * @inheritDoc
     */
    protected $skippedTests = [
        "testBasicUsageWithLongKey" => "Keys longer than 64 characters are unsupported.",
    ];

    /**
     * {@inheritDoc}
     *
     * @var \Psr\Cache\CacheItemPoolInterface&\Eufony\Cache\TagAwareInterface $cache
     */
    protected $cache;

    /**
     * @inheritDoc
     */
    public function createCachePool(): CacheItemPoolInterface&TagAwareInterface
    {
        return new TagAwareAdapter(new ArrayCache());
    }

    public function testBasicUsageWithTags(): void
    {
        if (isset($this->skippedTests[__FUNCTION__])) {
            $this->markTestSkipped($this->skippedTests[__FUNCTION__]);
        }


        $keys = ["key1", "key2", "key3"];
        $values = array_combine($keys, ["value1", "value2", "value3"]);
        $items = $this->cache->getItems($keys);

        foreach ($values as $key => $value) {
            $item = $items[$key];
            $item->set($value);
            $this->cache->saveDeferred($item);
        }

        $this->cache->commit();

        $this->cache->tag(["key1", "key2"], "tag1");
        $this->cache->tag("key2", "tag2");
        $this->cache->tag("key3", ["tag2", "tag3"]);

        $this->cache->invalidateTags("tag1");
        $this->assertFalse($this->cache->hasItem("key1"), "Key should have been deleted.");
        $this->assertFalse($this->cache->hasItem("key2"), "Key should have been deleted.");
        $this->assertTrue($this->cache->hasItem("key3"), "Key should not have been deleted.");
    }
}
