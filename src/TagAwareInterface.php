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

namespace Eufony\Cache;

/**
 * Provides a common interface for tag-based cache invalidation.
 */
interface TagAwareInterface
{
    /**
     * Appends the given tags to a cache key or an array of cache keys.
     *
     * Returns false if the keys could not be tagged, true otherwise.
     *
     * @param string|string[] $keys
     * @param string|string[] $tags
     * @return bool
     */
    public function tag(string|array $keys, string|array $tags): bool;

    /**
     * Invalidates the cache keys that are tagged with the given tags.
     *
     * Returns false if the keys could not be invalidated, true otherwise.
     *
     * @param string|string[] $tags
     * @return bool
     */
    public function invalidateTags(string|array $tags): bool;
}
