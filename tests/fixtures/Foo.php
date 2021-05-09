<?php declare(strict_types=1);

namespace Palmtree\Collection\Test\Fixture;

class Foo implements FooInterface
{
    public string $bar;

    public function __construct(string $bar = '')
    {
        $this->bar = $bar;
    }
}
