<?php

declare(strict_types=1);

namespace Palmtree\Collection;

use Palmtree\Collection\Exception\InvalidTypeException;

/**
 * @template T
 * @template-implements \IteratorAggregate<string, T>
 * @template-implements \ArrayAccess<string, T>
 */
class Map implements \ArrayAccess, \IteratorAggregate, \Countable, \JsonSerializable
{
    /** @use CollectionTrait<string, T> */
    use CollectionTrait;

    /**
     * Adds a set of elements to the collection.
     *
     * @psalm-param iterable<string,T> $elements
     *
     * @return $this
     *
     * @throws InvalidTypeException
     */
    public function add(iterable $elements): self
    {
        foreach ($elements as $key => $element) {
            // PHP converts numeric keys to integers, so we need to explicitly cast them to strings
            $this->set((string)$key, $element);
        }

        return $this;
    }

    /**
     * Adds a single element with the given key to the collection.
     *
     * @psalm-param T $element
     *
     * @throws Exception\InvalidTypeException
     */
    public function set(string $key, $element): self
    {
        $this->validator->validate($element);

        $this->elements[$key] = $element;

        foreach ($this->indexes as $index) {
            $index->add($key, $element);
        }

        return $this;
    }

    /**
     * Sorts the collection in-place, using an optional comparator function.
     *
     * @return $this
     */
    public function sort(?callable $comparator = null): self
    {
        if (!$comparator) {
            asort($this->elements);

            return $this;
        }

        uasort($this->elements, $comparator);

        return $this;
    }

    /**
     * @param string $offset
     * @psalm-param T $value
     *
     * @throws InvalidTypeException
     */
    public function offsetSet($offset, $value): void
    {
        $this->set($offset, $value);
    }

    /**
     * Returns a new collection from an array or iterable.
     *
     * @template V
     * @psalm-param array<string, V> $elements
     *
     * @return static
     * @psalm-return static<V>
     *
     * @throws InvalidTypeException
     */
    public static function fromArray(array $elements, ?string $type = null): self
    {
        return (new static($type))->add($elements);
    }

    /**
     * Returns a new collection from a JSON string.
     *
     * @return static
     *
     * @throws InvalidTypeException
     */
    public static function fromJson(string $json, ?string $type = null): self
    {
        return static::fromArray(json_decode($json, true), $type);
    }
}
