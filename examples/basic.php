<?php

use Palmtree\Collection\Collection;

require_once __DIR__ . '/../vendor/autoload.php';

// Objects of the same class
$objects = new Collection(\stdClass::class);

$item       = new \stdClass();
$item->data = 'Hello, World!';

$objects->set('key1', $item);

// Throws an InvalidArgumentException
//$objects->set('wont_work', ['Noop']);

$item2      = new \stdClass();
$item->data = 'Hello, again!';

$objects->set('key2', $item2);

// Arrays
$arrays = new Collection('array');

$items = [];

$items['key1'] = [true, 'Some value', 7];
$items['key2'] = [false, 'Another value', 8];

$arrays->add($items);

// Throws an InvalidTypeException
//$arrays->set('wont_work', 'Noop');
//$arrays->set('wont_work', 1);

/*$files = new Collection();
$files->setType(\SplFileObject::class);

$files->add([
    new SplFileObject(),
    new SplFileObject(),
])*/;

$closures = new Collection(\Closure::class);

$closures->push(function ($test) {
    echo $test;
});

$strings = new Collection('string');
// ...

$ints = new Collection('int');
// ...

$booleans = new Collection('bool');
// ...

$floats = Collection::fromArray([1.2, 43.9], 'float');
// ...
