# :palm_tree: Palmtree Collection

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

### Basic Usage

#### Map

```php
<?php
$map = new Map();

$map->set('foo', 'Bar');
$map->set('baz', true);

var_dump($map->get('baz')); // Returns true

// Array access
echo $map['foo']; // Prints Bar;

$map['bar'] = 'Baz';
```

#### Sequence

```php
<?php
$sequence = new Sequence();

$sequence->push('Foo');

// Supports chaining
$sequence
    ->push('Bar')
    ->push('Baz');

// Create instance from an array of integers
$sequence = Sequence::fromArray([1,2,3,4], 'int');
```

#### Instances of the same class

```php
<?php
$objects = new Map(\stdClass::class);

$element       = new \stdClass();
$element->data = 'Hello, World!';

$objects->set('key1', $element);
```

#### Single type

Can be anything returned by PHP's [gettype()](http://php.net/manual/en/function.gettype.php#refsect1-function.gettype-returnvalues) function as well as `float`, `int`, and `bool` for convenience.
```php
<?php
$floats = new Sequence('float');

$floats->push(3.4);
$floats->push(789.83);
```

## License

Released under the [MIT license](LICENSE)
