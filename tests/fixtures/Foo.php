<?php

namespace Palmtree\Collection\Test\Fixture;

class Foo implements FooInterface
{
    private string $bar;

    public function __construct(string $bar = '')
    {
        $this->bar = $bar;
    }

    public function getBar(): string
    {
        return $this->bar;
    }
}
