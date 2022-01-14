<?php

declare(strict_types=1);

namespace Palmtree\Collection;

use Palmtree\Collection\Exception\InvalidIndex;
use Palmtree\Collection\Exception\InvalidTypeException;
use Palmtree\Collection\Exception\OutOfBoundsException;
use Palmtree\Collection\Validator\TypeValidator;

/**
 * @template TKey as array-key
 * @template T
 */
trait CollectionTrait
{
    public TypeValidator $validator;
    /**
     * @var array<string, mixed>
     * @psalm-var array<TKey, T>
     */
    private array $elements = [];
    /** @var array<string, Index> */
    private array $indexes = [];

    final public function __construct(?string $type = null)
    {
        $this->validator = new TypeValidator($type);
    }

    /**
     * @psalm-param iterable<TKey,T> $elements
     *
     * @return $this
     */
    abstract public function add(iterable $elements): self;

    /** @return $this */
    abstract public function sort(?callable $comparator = null): self;

    /**
     * Returns a single element with the given key from the collection.
     *
     * @param string|int $key
     * @psalm-param TKey $key
     *
     * @psalm-return T
     */
    public function get($key)
    {
        if (!$this->containsKey($key)) {
            throw new OutOfBoundsException("Element with key '$key' does not exist");
        }

        return $this->elements[$key];
    }

    /**
     * Returns whether the given element is in the collection.
     *
     * @psalm-param T $element
     */
    public function contains($element, bool $strict = true): bool
    {
        return \in_array($element, $this->elements, $strict);
    }

    /**
     * Returns whether the given key exists in the collection.
     *
     * @param string|int $key
     * @psalm-param TKey $key
     */
    public function containsKey($key): bool
    {
        return isset($this->elements[$key]) || \array_key_exists($key, $this->elements);
    }

    /**
     * Removes an element with the given key from the collection.
     *
     * @param string|int $key
     * @psalm-param TKey $key
     *
     * @return $this
     */
    public function remove($key): self
    {
        foreach ($this->indexes as $index) {
            $index->remove((string)$key);
        }

        unset($this->elements[$key]);

        return $this;
    }

    /**
     * Removes an element from the collection.
     *
     * @psalm-param T $element
     *
     * @return $this
     */
    public function removeElement($element): self
    {
        $key = array_search($element, $this->elements, true);

        if ($key !== false) {
            $this->remove($key);
        }

        return $this;
    }

    /**
     * Returns a Sequence containing this collection's keys.
     *
     * @psalm-return Sequence<TKey>
     *
     * @throws InvalidTypeException
     */
    public function keys(): Sequence
    {
        return Sequence::fromArray(array_keys($this->elements));
    }

    /**
     * Returns a Sequence containing this collection's values.
     *
     * @psalm-return Sequence<T>
     *
     * @throws InvalidTypeException
     */
    public function values(): Sequence
    {
        return Sequence::fromArray(array_values($this->elements), $this->validator->getType());
    }

    /**
     * Clears all elements from the collection.
     */
    public function clear(): void
    {
        foreach ($this->indexes as $index) {
            $index->clear();
        }

        $this->elements = [];
    }

    /**
     * Returns the first element in the collection.
     *
     * @return mixed|null
     * @psalm-return T|null
     */
    public function first()
    {
        foreach ($this->elements as $element) {
            return $element;
        }

        return null;
    }

    /**
     * Returns the last element in the collection.
     *
     * @return mixed|null
     * @psalm-return T|null
     */
    public function last()
    {
        $lastKey = $this->lastKey();

        return $lastKey === null ? null : $this->elements[$lastKey];
    }

    /**
     * Returns the first key in the collection.
     *
     * @return string|int|null
     * @psalm-return TKey|null
     */
    public function firstKey()
    {
        return array_key_first($this->elements);
    }

    /**
     * Returns the last key in the collection.
     *
     * @return string|int|null
     * @psalm-return TKey|null
     */
    public function lastKey()
    {
        return array_key_last($this->elements);
    }

    /**
     * Returns the total number of elements in the collection.
     */
    public function count(): int
    {
        return \count($this->elements);
    }

    /**
     * Returns whether the collection is empty.
     */
    public function isEmpty(): bool
    {
        return empty($this->elements);
    }

    /**
     * Returns a new instance containing elements in the collection filtered by a predicate.
     *
     * @return static
     * @psalm-return static<T>
     */
    public function filter(?callable $predicate = null): self
    {
        if ($predicate !== null) {
            $values = array_filter($this->elements, $predicate, \ARRAY_FILTER_USE_BOTH);
        } else {
            $values = array_filter($this->elements);
        }

        return (new static($this->validator->getType()))->add($values);
    }

    /**
     * Returns a new instance containing elements mapped from the given callback.
     */
    public function map(callable $callback, ?string $type = null): self
    {
        $map = [];
        foreach ($this->elements as $key => $value) {
            $map[$key] = $callback($value, $key);
        }

        return (new static($type))->add($map);
    }

    /**
     * Returns whether at least one element passes the predicate function.
     */
    public function some(callable $predicate): bool
    {
        foreach ($this->elements as $key => $value) {
            if ($predicate($value, $key)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns whether all elements pass the predicate function.
     */
    public function every(callable $predicate): bool
    {
        foreach ($this->elements as $key => $value) {
            if (!$predicate($value, $key)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns the first element that passes the predicate function.
     *
     * @psalm-return T|null
     */
    public function find(callable $predicate)
    {
        foreach ($this->elements as $key => $value) {
            if ($predicate($value, $key)) {
                return $value;
            }
        }

        return null;
    }

    /**
     * Reduces the collection a single value.
     *
     * @see array_reduce()
     */
    public function reduce(callable $callback, $initial = null)
    {
        return array_reduce($this->elements, $callback, $initial);
    }

    /**
     * Reduces the collection a single value, iterating from right to left.
     *
     * @see array_reduce()
     */
    public function reduceRight(callable $callback, $initial = null)
    {
        return array_reduce(array_reverse($this->elements), $callback, $initial);
    }

    /**
     * Sorts and returns a copy of the collection using an optional comparator function.
     *
     * @return static
     * @psalm-return static<T>
     */
    public function sorted(?callable $comparator = null): self
    {
        $copy = (new static($this->validator->getType()))->add($this->elements);
        $copy->sort($comparator);

        return $copy;
    }

    /**
     * Returns the collection as an array.
     *
     * @psalm-return array<TKey,T>
     */
    public function toArray(): array
    {
        return $this->elements;
    }

    /**
     * @psalm-return T
     *
     * @throws InvalidIndex|OutOfBoundsException
     */
    public function getBy(string $indexId, string $key)
    {
        if (!isset($this->indexes[$indexId])) {
            throw new InvalidIndex($indexId);
        }

        try {
            $key = $this->indexes[$indexId]->get($key);
        } catch (OutOfBoundsException $e) {
            throw new OutOfBoundsException("Key '$key' does not exist within index '$indexId'");
        }

        return $this->get($key);
    }

    /**
     * @psalm-param callable(T): string $callback
     *
     * @return $this
     */
    public function addIndex(string $id, callable $callback): self
    {
        $index = new Index($callback);

        foreach ($this->elements as $key => $element) {
            $index->add((string)$key, $element);
        }

        $this->indexes[$id] = $index;

        return $this;
    }

    /**
     * @return $this
     */
    public function removeIndex(string $id): self
    {
        unset($this->indexes[$id]);

        return $this;
    }

    /**
     * @return \ArrayIterator<TKey, T>
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->elements);
    }

    /**
     * @param string|int $offset
     * @psalm-param TKey $offset
     */
    public function offsetExists($offset): bool
    {
        return $this->containsKey($offset);
    }

    /**
     * @param string|int $offset
     * @psalm-param TKey $offset
     *
     * @return mixed|null
     * @psalm-return T
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @param string|int $offset
     * @psalm-param TKey $offset
     */
    public function offsetUnset($offset): void
    {
        $this->remove($offset);
    }

    public function jsonSerialize(): array
    {
        return $this->elements;
    }
}
