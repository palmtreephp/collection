<?php

namespace Palmtree\Collection;

class Index
{
    private \Closure $callback;
    private array $index;

    public function __construct(callable $callback)
    {
        $this->callback = \Closure::fromCallable($callback);
        $this->index = [];
    }

    /**
     * @return mixed|null
     */
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

    /**
     * @param mixed $element
     */
    public function add(string $key, $element): self
    {
        $callback = $this->callback;

        $this->index[$callback($element)] = $key;

        return $this;
    }
}
