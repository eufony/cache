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

namespace Eufony\Cache\Tests\Unit\Utils;

use Eufony\Cache\InvalidArgumentException;
use Eufony\Cache\Utils\CacheKeyProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * Unit tests for `\Eufony\Cache\Utils\CacheKeyProvider\`.
 */
class CacheKeyProviderTest extends TestCase
{
    /**
     * Data provider for invalid parameters for the `CacheKeyProvider::get()`
     * method.
     *
     * Returns an object that cannot be used as a parameter for the method for each
     * data set.
     *
     * @return mixed[][]
     */
    public function invalidParameters(): array
    {
        $class = new class() {
        };

        $invalid_objects = [
            new $class(),
        ];

        return array_map(fn($object) => [$object], $invalid_objects);
    }

    public function testGetWithValidParameters(): void
    {
        $objects = [
            "",
            "foo",
            str_repeat("a", 1024 * 1024),
            0,
            PHP_INT_MIN,
            PHP_INT_MAX,
            4.2,
            PHP_FLOAT_MIN,
            PHP_FLOAT_MAX,
            true,
            false,
            null,
            ["foo", "bar", "baz"],
            ["foo" => "bar"],
            [["foo", "bar"], ["foo", "baz"]],
            [["foo" => "bar"], ["foo" => "baz"]],
            new stdClass(),
        ];

        $keys = [];

        foreach ($objects as $object) {
            $key = CacheKeyProvider::get($object);

            $this->assertMatchesRegularExpression("/^[a-zA-Z0-9_.]{1,64}$/", $key, "Returned key is invalid.");

            $this->assertArrayNotHasKey($key, $keys, "Returned key is not unique.");
            $keys[$key] = null;

            $this->assertSame($key, CacheKeyProvider::get($object), "Method is not deterministic.");
        }
    }

    /**
     * @dataProvider invalidParameters
     */
    public function testGetWithInvalidParameters(mixed $object): void
    {
        $this->expectException(InvalidArgumentException::class);
        CacheKeyProvider::get($object);
    }
}
