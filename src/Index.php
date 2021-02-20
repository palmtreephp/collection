<?php

namespace Palmtree\Collection;

use Palmtree\Collection\Exception\OutOfBoundsException;

/**
 * @template T
 * @template TKey of array-key
 * @template TElementKey
 */
class Index
{
    /** @psalm-var \Closure(T): TKey */
    private \Closure $callback;
    /** @psalm-var array<TKey, TElementKey> */
    private array $index = [];

    /** @psalm-param callable(T): TKey $callback */
    public function __construct(callable $callback)
    {
        $this->callback = \Closure::fromCallable($callback);
    }

    /**
     * @psalm-param TKey $key
     * @psalm-return TElementKey
     */
    public function get(string $key): string
    {
        if (!$this->has($key)) {
            throw new OutOfBoundsException("Key '$key' does not exist within index");
        }

        return $this->index[$key];
    }

    /**
     * @psalm-param TKey $key
     */
    public function has(string $key): bool
    {
        return isset($this->index[$key]) || \array_key_exists($key, $this->index);
    }

    /**
     * @psalm-param TKey $key
     */
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
     * @psalm-param TElementKey $key
     *
     * @param mixed $element
     * @psalm-param T $element
     */
    public function add(string $key, $element): self
    {
        $this->index[($this->callback)($element)] = $key;

        return $this;
    }
}
