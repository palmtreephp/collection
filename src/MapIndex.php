<?php

namespace Palmtree\Collection;

class MapIndex
{
    /** @var callable */
    private $callback;
    /** @var array */
    private $index;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
        $this->index    = [];
    }

    public function get(string $key)
    {
        return $this->index[$key] ?? null;
    }

    public function remove(string $key): self
    {
        unset($this->index[$key]);

        return $this;
    }

    public function clear(): self
    {
        $this->index = [];

        return $this;
    }

    public function add(string $key, $element): self
    {
        $callback = $this->callback;

        $this->index[$callback($element)] = $key;

        return $this;
    }
}
