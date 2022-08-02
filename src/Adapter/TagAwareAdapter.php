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

namespace Eufony\Cache\Adapter;

use Eufony\Cache\TagAwareInterface;
use Eufony\Cache\Utils\CacheKeyProvider;
use Eufony\Cache\Utils\CacheProxyTrait;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Provides a caching implementation that wraps another implementation and adds
 * functionality for tag-based cache invalidation.
 */
class TagAwareAdapter implements CacheItemPoolInterface, TagAwareInterface
{
    use CacheProxyTrait;

    /**
     * The PSR-6 cache used internally to store a mapping of tags to cache keys.
     *
     * @var \Psr\Cache\CacheItemPoolInterface $tagCache
     */
    protected CacheItemPoolInterface $tagCache;

    /**
     * Class constructor.
     * Wraps another caching implementation and provides functionality for
     * tag-based cache invalidation.
     *
     * Optionally accepts a secondary cache pool for storing the tags.
     * If no secondary cache is given, the primary pool is used instead.
     *
     * @param \Psr\Cache\CacheItemPoolInterface $cache
     * @param \Psr\Cache\CacheItemPoolInterface|null $tagCache
     */
    public function __construct(CacheItemPoolInterface $cache, CacheItemPoolInterface $tagCache = null)
    {
        $this->cache = $cache;
        $this->tagCache = $tagCache ?? $cache;
    }

    /**
     * Returns the internal PSR-6 cache that is used to store the tags.
     *
     * @return \Psr\Cache\CacheItemPoolInterface
     */
    public function tagCache(): CacheItemPoolInterface
    {
        return $this->tagCache;
    }

    /**
     * @inheritDoc
     */
    public function tag(string|array $keys, string|array $tags): bool
    {
        if (is_string($keys)) {
            $keys = [$keys];
        }

        if (is_string($tags)) {
            $tags = [$tags];
        }

        $tags = array_map(fn($tag) => CacheKeyProvider::get($tag), $tags);
        $items = $this->tagCache->getItems($tags);
        $success = true;

        foreach ($tags as $tag) {
            $item = $items[$tag];
            $value = $item->isHit() ? $item->get() : [];
            $item->set([...$value, ...$keys]);
            $success = $this->tagCache->saveDeferred($item) && $success;
        }

        $success = $this->tagCache->commit() && $success;

        return $success;
    }

    /**
     * @inheritDoc
     */
    public function invalidateTags(string|array $tags): bool
    {
        if (is_string($tags)) {
            $tags = [$tags];
        }

        $tags = array_map(fn($tag) => CacheKeyProvider::get($tag), $tags);
        $items = $this->tagCache->getItems($tags);
        $success = true;

        foreach ($items as $item) {
            $value = $item->isHit() ? $item->get() : [];
            $success = $this->tagCache->deleteItems($value) && $success;
        }

        $success = $this->tagCache->deleteItems($tags) && $success;

        return $success;
    }
}
