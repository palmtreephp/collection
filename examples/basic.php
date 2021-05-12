<?php

declare(strict_types=1);

use Palmtree\Collection\Map;
use Palmtree\Collection\Sequence;

require_once __DIR__ . '/../vendor/autoload.php';

// Objects of the same class
$objects = new Map(\stdClass::class);

$element       = new \stdClass();
$element->data = 'Hello, World!';

$objects->set('key1', $element);

// Throws an InvalidArgumentException
//$objects->set('wont_work', ['Noop']);

$element2      = new \stdClass();
$element->data = 'Hello, again!';

$objects->set('key2', $element2);

// Arrays
$arrays = new Map('array');

$elements = [];

$elements['key1'] = [true, 'Some value', 7];
$elements['key2'] = [false, 'Another value', 8];

$arrays->add($elements);

// Throws an InvalidTypeException
//$arrays->set('wont_work', 'Noop');
//$arrays->set('wont_work', 1);

$files = new Sequence(\SplFileObject::class);

$files->add([
    new \SplFileObject('foo.txt'),
    new \SplFileObject('bar.txt'),
]);

$closures = new Sequence(\Closure::class);

$closures->push(static function ($test) {
    echo $test;
});

$strings = new Map('string');
// ...

$ints = new Map('int');
// ...

$booleans = new Map('bool');
// ...

$floats = Sequence::fromArray([1.2, 43.9], 'float');
// ...
