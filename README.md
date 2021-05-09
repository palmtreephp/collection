# :palm_tree: Palmtree Collection

[![License](http://img.shields.io/packagist/l/palmtree/collection.svg)](LICENSE)
[![Build Status](https://scrutinizer-ci.com/g/palmtreephp/collection/badges/build.png?b=master)](https://scrutinizer-ci.com/g/palmtreephp/collection/build-status/master)
[![Scrutinizer](https://img.shields.io/scrutinizer/g/palmtreephp/collection.svg)](https://scrutinizer-ci.com/g/palmtreephp/collection/)
[![Scrutinizer Coverage](https://img.shields.io/scrutinizer/coverage/g/palmtreephp/collection.svg)](https://scrutinizer-ci.com/g/palmtreephp/collection/)

Maps and Sequences with optional type validation.

## Requirements
* PHP >= 7.4

For PHP 7.1 support use [v3.0](https://github.com/palmtreephp/collection/tree/v3.0.0)

## Installation

Use composer to add the package to your dependencies:
```bash
composer require palmtree/collection
```

## Usage

### Basic Usage

#### Map

Maps are key-value data structures where performance is O(1) for `get()` and `containsKey()` methods.

```php
$map = new Map();

// Supports chaining
$map
    ->set('foo', 'Bar')
    ->set('baz', true);

$map->get('baz'); // Returns true

// Array access
echo $map['foo']; // Prints Bar;

$map['bar'] = 'Baz';
```

#### Sequence

Sequences - sometimes referred to as Lists - are data structures containing a linear, sequential set of values.

```php
$sequence = new Sequence();

$sequence->push('Foo');

// Sequence::push() is variadic as per array_push()
$sequence->push('Bar', 'Baz');

// Create instance from an array of integers
$sequence = Sequence::fromArray([1,2,3,4], 'int');

// Maps and Sequences are both traversable
foreach($sequence as $integer) {
}
```

#### Instances of the same class

Ensure each element is an instance of a particular class or interface

```php
$objects = new Map(\stdClass::class);

$element       = new \stdClass();
$element->data = 'Hello, World!';

$objects->set('key1', $element);
```

```php
$foos = new Sequence(FooInterface::class);

$foos->push(new Foo());
```

#### Primitive types

Can be one of `string`, `int`, `float`, `bool`, `array`, `object`:

```php
$floats = new Sequence('float');

$floats->push(3.4, 789, 83);
```

#### Custom Indexes

Custom indexes may be added to a collection to enable `O(1)` (constant as per `isset`) lookups instead of `O(n)` (linear as per `in_array`):

The `addIndex` method takes an index key and a callback. The callback takes a single element from the collection and must
return an integer or string to be used as the index value.

The example below shows how to add a custom ID index where the callback returns an object's ID value:

```php
$objects = new Map(\stdClass::class);

$object1     = new \stdClass();
$object1->id = 'foo';

$object2     = new \stdClass();
$object2->id = 'bar';

$objects
    ->set('key1', $object1)
    ->set('key2', $object2);

$objects->addIndex('id', fn(\stdClass $object) => $object->id);

$object1 = $objects->getBy('id', 'foo');
$object2 = $objects->getBy('id', 'bar');
```

## License

Released under the [MIT license](LICENSE)
