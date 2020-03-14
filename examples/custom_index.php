<?php

use Palmtree\Collection\Map;

require_once __DIR__ . '/../vendor/autoload.php';

$objects = new Map(\stdClass::class);

$object1     = new \stdClass();
$object1->id = 'foo';

$object2     = new \stdClass();
$object2->id = 'bar';

$objects
    ->set('key1', $object1)
    ->set('key2', $object2)
    ->addIndex('id', static function (\stdClass $object) {
        return $object->id;
    });

var_dump($objects->getBy('id', 'foo') === $object1);
var_dump($objects->getBy('id', 'bar') === $object2);
