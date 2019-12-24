<?php

namespace Palmtree\Collection\Test\Fixture;

class Foo implements FooInterface
{
    /** @var string */
    private $bar;

    public function __construct(?string $bar = null)
    {
        $this->bar = $bar;
    }

    public function getBar(): ?string
    {
        return $this->bar;
    }
}
