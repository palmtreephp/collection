<?php

use Palmtree\Collection\Sequence;

require_once __DIR__ . '/../vendor/autoload.php';

$sequence = new Sequence('stdClass');

$obj1        = new \stdClass();
$obj1->price = 10;

$obj2        = new \stdClass();
$obj2->price = 20;

$obj3        = new \stdClass();
$obj3->price = 30;

$obj4        = new \stdClass();
$obj4->price = 40;

$sequence->push($obj1, $obj2, $obj3, $obj4);

$filtered = $sequence
    ->filter(function (\stdClass $obj) {
        return $obj->price < 40;
    })->sort(function (\stdClass $a, \stdClass $b) {
        return $b->price <=> $a->price;
    });

$total = $filtered->reduce(function ($total, \stdClass $obj) {
    return $total + $obj->price;
}, 0);

$p = 1;
