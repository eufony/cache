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

namespace Eufony\Cache\Marshaller;

/**
 * Provides a marshaller implementation using the PHP built-in `serialize()`
 * and `unserialize()` methods.
 *
 * Because the PSR-6 standards require that all cache values must be
 * serializable, this type of marshalling is compatible with all valid values.
 */
class SerializeMarshaller implements MarshallerInterface
{
    /**
     * @inheritDoc
     */
    public function marshall(mixed $value): string
    {
        return serialize($value);
    }

    /**
     * @inheritDoc
     */
    public function unmarshall(string $value): mixed
    {
        return unserialize($value);
    }
}
