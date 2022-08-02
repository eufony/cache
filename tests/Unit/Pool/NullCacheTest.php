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

namespace Eufony\Cache\Tests\Unit\Pool;

use Cache\IntegrationTests\CachePoolTest;
use Eufony\Cache\Pool\NullCache;
use Psr\Cache\CacheItemPoolInterface;

/**
 * PSR-6 integration tests for `\Eufony\Cache\NullCache`.
 */
class NullCacheTest extends CachePoolTest
{
    /**
     * @inheritDoc
     */
    protected $skippedTests = [
        "testBasicUsageWithLongKey" => "Keys longer than 64 characters are unsupported.",
        "testBasicUsage" => "Not applicable to NullCache.",
        "testBinaryData" => "Not applicable to NullCache.",
        "testCommit" => "Not applicable to NullCache.",
        "testDataTypeArray" => "Not applicable to NullCache.",
        "testDataTypeBoolean" => "Not applicable to NullCache.",
        "testDataTypeFloat" => "Not applicable to NullCache.",
        "testDataTypeInteger" => "Not applicable to NullCache.",
        "testDataTypeNull" => "Not applicable to NullCache.",
        "testDataTypeObject" => "Not applicable to NullCache.",
        "testDataTypeString" => "Not applicable to NullCache.",
        "testDeferredSave" => "Not applicable to NullCache.",
        "testDeferredSaveWithoutCommit" => "Not applicable to NullCache.",
        "testDeleteDeferredItem" => "Not applicable to NullCache.",
        "testDeleteItems" => "Not applicable to NullCache.",
        "testExpiresAfterWithNull" => "Not applicable to NullCache.",
        "testExpiresAt" => "Not applicable to NullCache.",
        "testExpiresAtWithNull" => "Not applicable to NullCache.",
        "testGetItem" => "Not applicable to NullCache.",
        "testGetItems" => "Not applicable to NullCache.",
        "testHasItem" => "Not applicable to NullCache.",
        "testIsHit" => "Not applicable to NullCache.",
        "testIsHitDeferred" => "Not applicable to NullCache.",
        "testKeyLength" => "Not applicable to NullCache.",
        "testSave" => "Not applicable to NullCache.",
        "testSaveDeferredOverwrite" => "Not applicable to NullCache.",
        "testSaveDeferredWhenChangingValues" => "Not applicable to NullCache.",
        "testSaveWithoutExpire" => "Not applicable to NullCache.",
        "testSavingObject" => "Not applicable to NullCache.",
    ];

    /**
     * @inheritDoc
     */
    public function createCachePool(): CacheItemPoolInterface
    {
        return new NullCache();
    }
}
