<?php

declare(strict_types=1);

namespace Palmtree\Collection;

use Palmtree\Collection\Exception\BadMethodCallException;
use Palmtree\Collection\Exception\InvalidTypeException;

/**
 * @template T
 * @template-implements \IteratorAggregate<int, T>
 * @template-implements \ArrayAccess<int, T>
 */
class Sequence implements \ArrayAccess, \IteratorAggregate, \Countable, \JsonSerializable
{
    /** @use CollectionTrait<int, T> */
    use CollectionTrait;

    /**
     * Adds a set of elements to the collection.
     *
     * @psalm-param iterable<int, T> $elements
     *
     * @return $this
     *
     * @throws InvalidTypeException
     */
    public function add(iterable $elements): self
    {
        foreach ($elements as $element) {
            $this->push($element);
        }

        return $this;
    }

    /**
     * Pushes one or more elements on to the end of the sequence.
     *
     * @param mixed ...$elements
     * @psalm-param T ...$elements
     *
     * @psalm-return $this
     *
     * @psalm-suppress InvalidPropertyAssignmentValue
     *
     * @throws InvalidTypeException
     */
    public function push(...$elements): self
    {
        foreach ($elements as $element) {
            $this->validator->validate($element);

            $this->elements[] = $element;
        }

        $this->reindex();

        return $this;
    }

    /**
     * Pops and returns the last element of the sequence, shortening the sequence by one element.
     *
     * @psalm-return T
     *
     * @see array_pop
     */
    public function pop()
    {
        $popped = array_pop($this->elements);

        $this->reindex();

        return $popped;
    }

    /**
     * Shifts an element off the beginning of sequence and returns it.
     *
     * @psalm-return T
     *
     * @see array_shift
     */
    public function shift()
    {
        $shifted = array_shift($this->elements);

        $this->reindex();

        return $shifted;
    }

    /**
     * Prepends one or more elements to the beginning of the sequence.
     *
     * @param mixed ...$elements
     * @psalm-param T ...$elements
     *
     * @throws InvalidTypeException
     *
     * @see array_unshift
     */
    public function unshift(...$elements): int
    {
        foreach ($elements as $element) {
            $this->validator->validate($element);
        }

        /** @psalm-suppress InvalidPropertyAssignmentValue */
        $result = array_unshift($this->elements, ...$elements);

        $this->reindex();

        return $result;
    }

    /**
     * Sorts the collection in-place, using an optional comparator function.
     *
     * @return $this
     */
    public function sort(?callable $comparator = null): self
    {
        if (!$comparator) {
            /** @psalm-suppress InvalidPropertyAssignmentValue */
            sort($this->elements);

            return $this;
        }

        /** @psalm-suppress InvalidPropertyAssignmentValue */
        usort($this->elements, $comparator);

        $this->reindex();

        return $this;
    }

    private function reindex(): void
    {
        if (empty($this->indexes)) {
            return;
        }

        foreach ($this->indexes as $index) {
            $index->clear();
        }

        foreach ($this->elements as $key => $value) {
            foreach ($this->indexes as $index) {
                $index->add((string)$key, $value);
            }
        }
    }

    /**
     * @param int|null $offset
     * @psalm-param T $value
     *
     * @throws InvalidTypeException
     */
    public function offsetSet($offset, $value): void
    {
        if ($offset !== null) {
            throw new BadMethodCallException("Cannot set element at offset $offset. Sequences must be sequential");
        }

        $this->push($value);
    }

    /**
     * Returns a new collection from an array or iterable.
     *
     * @template V
     * @psalm-param list<V> $elements
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
