<?php

declare(strict_types=1);

use Palmtree\Collection\Sequence;

require_once __DIR__ . '/../vendor/autoload.php';

$sequence = new Sequence('stdClass');

$obj1 = new \stdClass();
$obj1->price = 10;

$obj2 = new \stdClass();
$obj2->price = 20;

$obj3 = new \stdClass();
$obj3->price = 30;

$obj4 = new \stdClass();
$obj4->price = 40;

$sequence->push($obj1, $obj2, $obj3, $obj4);

$total = $sequence
    ->filter(fn (stdClass $obj) => $obj->price < 40)
    ->sort(fn (stdClass $a, stdClass $b) => $b->price <=> $a->price)
    ->reduce(fn ($total, stdClass $obj) => $total + $obj->price, 0);
