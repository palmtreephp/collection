# Palmtree Collection

[![License](http://img.shields.io/packagist/l/palmtree/collection.svg)](LICENSE)
[![Build Status](https://scrutinizer-ci.com/g/palmtreephp/collection/badges/build.png?b=master)](https://scrutinizer-ci.com/g/palmtreephp/collection/build-status/master)
[![Scrutinizer](https://img.shields.io/scrutinizer/g/palmtreephp/collection.svg)](https://scrutinizer-ci.com/g/palmtreephp/collection/)
[![Scrutinizer Coverage](https://img.shields.io/scrutinizer/coverage/g/palmtreephp/collection.svg)](https://scrutinizer-ci.com/g/palmtreephp/collection/)

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
// Sequence
$sequence = new Collection();

$sequence->push('Foo');

// Supports chaining
$sequence
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
$objects = new Collection(\stdClass::class);

$item       = new \stdClass();
$item->data = 'Hello, World!';

$objects->set('key1', $item);
```

#### Single type

Can be anything returned by PHP's [gettype()](http://php.net/manual/en/function.gettype.php#refsect1-function.gettype-returnvalues) function as well as `float`, `int`, and `bool` for convenience.
```php
<?php
$floats = new Collection('float');

$floats->push(3.4);
$floats->push(789.83);
```

## License

Released under the [MIT license](LICENSE)
