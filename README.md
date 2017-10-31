# Palmtree Collection

[![License](http://img.shields.io/packagist/l/palmtree/collection.svg)](LICENSE)
[![Build Status](https://scrutinizer-ci.com/g/palmtreephp/collection/badges/build.png?b=master)](https://scrutinizer-ci.com/g/palmtreephp/collection/build-status/master)
[![Scrutinizer](https://img.shields.io/scrutinizer/g/palmtreephp/collection.svg)](https://scrutinizer-ci.com/g/palmtreephp/csv/)
[![Scrutinizer Coverage](https://img.shields.io/scrutinizer/coverage/g/palmtreephp/collection.svg)](https://scrutinizer-ci.com/g/palmtreephp/csv/)

Collection class with optional type validation.

## Requirements
* PHP >= 5.6

## Installation

Use composer to add the package to your dependencies:
```bash
composer require palmtree/collection
```

## Usage

#### Basic Usage
```php
<?php
use Palmtree\Collection\Collection;

require_once __DIR__ . '/../vendor/autoload.php';

// Sequence
$sequence = new Collection();

$sequence
    ->push('Foo')
    ->push('Bar')
    ->push('Baz');

// Map
$map = new Collection();

$map->set('foo', 'Bar');
$map->set('baz', true);

// Create instance from an array of integers
$collection = Collection::fromArray([1,2,3,4], 'int');
```

#### Instances of the same class

```php
<?php
use Palmtree\Collection\Collection;

require_once __DIR__ . '/../vendor/autoload.php';

$objects = new Collection();

$objects->setType(\stdClass::class);

$item       = new \stdClass();
$item->data = 'Hello, World!';

$objects->set('key1', $item);
```

#### Single type

Can be anything returned by PHP's [gettype()](http://php.net/manual/en/function.gettype.php#refsect1-function.gettype-returnvalues) function or `float`, `int`, and `bool` for convenience.
```php
<?php
// Arrays
$arrays = new Collection();
$arrays->setType('array');

$items = [];

$items['key1'] = [true, 'Some value', 7];
$items['key2'] = [false, 'Another value', 8];

$arrays->add($items);
```

## License

Released under the [MIT license](LICENSE)
