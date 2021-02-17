<?php

namespace Palmtree\Collection;

use Palmtree\Collection\Exception\OutOfBoundsException;

class Index
{
    private \Closure $callback;
    private array $index;

    public function __construct(callable $callback)
    {
        $this->callback = \Closure::fromCallable($callback);
        $this->index    = [];
    }

    /**
     * @return mixed
     */
    public function get(string $key)
    {
        if (!$this->has($key)) {
            throw new OutOfBoundsException("Key '$key' does not exist within index");
        }

        return $this->index[$key];
    }

    public function has(string $key): bool
    {
        return isset($this->index[$key]) || \array_key_exists($key, $this->index);
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
        $this->index[($this->callback)($element)] = $key;

        return $this;
    }
}
