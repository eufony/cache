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

namespace Eufony\Cache\Tests\Integration\Adapter;

use Cache\IntegrationTests\SimpleCacheTest;
use Eufony\Cache\Adapter\Psr16Adapter;
use Eufony\Cache\Pool\ArrayCache;
use Psr\SimpleCache\CacheInterface;

/**
 * PSR-16 integration tests for `\Eufony\Cache\Adapter\Psr16Adapter`.
 */
class Psr16AdapterTest extends SimpleCacheTest
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
        return new Psr16Adapter(new ArrayCache());
    }
}
