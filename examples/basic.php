<?php

use Palmtree\Collection\Collection;

require_once __DIR__ . '/../vendor/autoload.php';

// Objects of the same class
$objects = new Collection();

$objects->setType(\stdClass::class);

$item       = new \stdClass();
$item->data = 'Hello, World!';

$objects->set('key1', $item);

// Throws an InvalidArgumentException
//$objects->set('wont_work', ['Noop']);

$item2      = new \stdClass();
$item->data = 'Hello, again!';

$objects->set('key2', $item2);

// Arrays
$arrays = new Collection();
$arrays->setType('array');

$items = [];

$items['key1'] = [true, 'Some value', 7];
$items['key2'] = [false, 'Another value', 8];

$arrays->add($items);

// Throws an InvalidArgumentException
//$arrays->set('wont_work', 'Noop');
//$arrays->set('wont_work', 1);

$files = new Collection();
$files->setType(\SplFileObject::class);

$files->add([
    new SplFileObject('/path/to/some/file.ext'),
    new SplFileObject('/path/to/some/file2.ext'),
]);

$strings = new Collection();
$strings->setType('string');
// ...

$ints = new Collection();
$ints->setType('integer');
// ...

$booleans = new Collection();
$booleans->setType('boolean');
// ...

$floats = new Collection();
$floats->setType('float');
// ...