<?php

namespace Palmtree\Collection\Test\Fixture;

class Foo implements FooInterface
{
    public string $bar;

    public function __construct(string $bar = '')
    {
        $this->bar = $bar;
    }
}
