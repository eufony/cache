<h1 align="center">The Eufony Cache Package</h1>

<p align="center">
    <a href="https://packagist.org/packages/eufony/cache">
        <img alt="Packagist Downloads" src="https://img.shields.io/packagist/dt/eufony/cache?label=Packagist%20Downloads">
    </a>
    <a href="https://github.com/eufony/cache">
        <img alt="GitHub Stars" src="https://img.shields.io/github/stars/eufony/cache?label=GitHub%20Stars">
    </a>
    <a href="https://github.com/eufony/cache/issues">
        <img alt="Issues" src="https://img.shields.io/github/issues/eufony/cache/open?label=Issues">
    </a>
    <br>
    <a href="https://github.com/eufony/cache#license">
        <img alt="License" src="https://img.shields.io/github/license/eufony/cache?label=License">
    </a>
    <a href="https://github.com/eufony/cache#contributing">
        <img alt="Community Built" src="https://img.shields.io/badge/Made%20with-%E2%9D%A4-red">
    </a>
</p>

*eufony/cache provides the most trivial implementations of PSR-6 and PSR-16 as well as other essentials for getting
started with caching.*

*eufony/cache* is a small PHP library that handles basic caching needs without overcomplicating things. It implements
the simplest kinds cache pools, supporting both the [PSR-6](https://www.php-fig.org/psr/psr-6/)
and [PSR-16](https://www.php-fig.org/psr/psr-16/) standards, plus:

- Adapter classes to convert between PSR-6 and PSR-16 caching implementations.
- Utility classes to aid in the implementation of other caching backends.
- An extension to the caching standards using a tag-based cache invalidation technique.
- A framework to hook in to and modify the cache values on their way in and out of the pool.

Interested? [Here's how to get started.](#getting-started)

## Getting started

### Installation

*eufony/cache* is released as a [Packagist](https://packagist.org/) package and can be easily installed
via [Composer](https://getcomposer.org/) with:

    composer require "eufony/cache"

### Basic Usage

*For a more detailed documentation, see [here](docs).*

*eufony/cache* provides three different caching implementations:

```php
// An in-memory cache pool using a PHP array.
$cache = new ArrayCache();

// An in-memory cache pool using the `apcu` extension,
// which can share cache values between processes on the same host.
$cache = new ApcuCache();

// A fake cache based on the Null Object Pattern.
$cache = new NullCache();
```

You can extend these cache pools using [marshallers](https://en.wikipedia.org/wiki/Marshalling_(computer_science)) and a
tag-based cache invalidation interface.

It also provides adapter classes to convert between PSR-6 and PSR-16 cache implementations:

```php
// Convert from PSR-6 to PSR-16.
$cache = new Psr16Adapter(/* ... */);

// Convert from PSR-16 to PSR-6.
$cache = new Psr6Adapter(/* ... */);
```

## Contributing

Found a bug or a missing feature? You can report it over at the [issue tracker](https://github.com/eufony/cache/issues).

## License

This program is free software: you can redistribute it and/or modify it under the terms of the GNU Lesser General Public
License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more
details.

You should have received a copy of the GNU Lesser General Public License along with this program. If not,
see <https://www.gnu.org/licenses/>.
