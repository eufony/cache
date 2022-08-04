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

namespace Eufony\Cache\Adapter;

use Eufony\Cache\TagAwareInterface;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Provides a wrapper class to adapt a PSR-6 caching implementation to support
 * both the PSR-16 standards and `\Eufony\Cache\TagAwareInterface`.
 *
 * @deprecated This class is a temporary solution until a more robust proxy
 * management solution is implemented by the Eufony project.
 */
class TagAwarePsr16Adapter extends Psr16Adapter implements TagAwareInterface
{
    /**
     * {@inheritDoc}
     *
     * @var \Psr\Cache\CacheItemPoolInterface&\Eufony\Cache\TagAwareInterface $cache
     */
    protected CacheItemPoolInterface $cache;

    /**
     * {@inheritDoc}
     *
     * Additionally implements `\Eufony\Cache\TagAwareInterface`.
     *
     * @param \Psr\Cache\CacheItemPoolInterface $cache
     *
     * @deprecated
     */
    public function __construct(CacheItemPoolInterface $cache)
    {
        parent::__construct(new TagAwareAdapter($cache));
    }

    /**
     * @inheritDoc
     */
    public function tag(array|string $keys, string|array $tags): bool
    {
        return $this->cache->tag($keys, $tags);
    }

    /**
     * @inheritDoc
     */
    public function invalidateTags(string|array $tags): bool
    {
        return $this->cache->invalidateTags($tags);
    }
}
