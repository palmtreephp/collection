<?php

namespace Palmtree\Collection;

/**
 * @template TKey of array-key
 * @template T
 * @extends IteratorAggregate<TKey, T>
 * @extends ArrayAccess<TKey|null, T>
 */
interface CollectionInterface extends \ArrayAccess, \IteratorAggregate, \Countable, \JsonSerializable
{
    /**
     * Returns a single element with the given key from the collection.
     *
     * @param string|int $key
     * @psalm-param TKey $key
     *
     * @return mixed
     * @psalm-return T
     */
    public function get($key);

    /**
     * Adds a set of elements to the collection.
     *
     * @psalm-param iterable<TKey,T> $elements
     * @psalm-return static<TKey,T>
     */
    public function add(iterable $elements): self;

    /**
     * Removes an element with the given key from the collection.
     *
     * @param string|int $key
     * @psalm-param TKey $key
     */
    public function remove($key): self;

    /**
     * Removes an element from the collection.
     *
     * @param mixed $element
     * @psalm-param T $element
     */
    public function removeElement($element): self;

    /**
     * Clears all elements from the collection.
     */
    public function clear(): self;

    /**
     * Returns the entire collection as an array.
     */
    public function all(): array;

    /**
     * Returns the first element in the collection.
     *
     * @return mixed|null
     * @psalm-return T|null
     */
    public function first();

    /**
     * Returns the last element in the collection.
     *
     * @return mixed|null
     * @psalm-return T|null
     */
    public function last();

    /**
     * Returns the first key in the collection.
     *
     * @return string|int|null
     * @psalm-return TKey|null
     */
    public function firstKey();

    /**
     * Returns the last key in the collection.
     *
     * @return string|int|null
     * @psalm-return TKey|null
     */
    public function lastKey();

    /**
     * Returns a Sequence containing this collection's keys.
     *
     * @return Sequence
     * @psalm-return Sequence<TKey>
     */
    public function keys(): self;

    /**
     * Returns a Sequence containing this collection's values.
     *
     * @return Sequence
     * @psalm-return Sequence<T>
     */
    public function values(): self;

    /**
     * Returns whether the given element is in the collection.
     *
     * @param mixed $element
     * @psalm-param T $element
     */
    public function contains($element, bool $strict = true): bool;

    /**
     * Returns whether the given key exists in the collection.
     *
     * @param string|int $key
     * @psalm-param TKey $key
     */
    public function containsKey($key): bool;

    /**
     * Returns a new instance containing elements mapped from the given callback.
     */
    public function map(callable $callback, ?string $type = null): self;

    /**
     * Returns a new instance containing elements in the collection filtered by a predicate.
     */
    public function filter(?callable $predicate = null): self;

    /**
     * Returns whether at least one element passes the predicate function.
     */
    public function some(callable $predicate): bool;

    /**
     * Returns whether all elements pass the predicate function.
     */
    public function every(callable $predicate): bool;

    /**
     * Returns the first element that passes the predicate function.
     *
     * @return mixed
     * @psalm-return T
     */
    public function find(callable $predicate);

    /**
     * Reduces the collection a single value.
     *
     * @param mixed $initial
     *
     * @return mixed
     *
     * @see array_reduce()
     */
    public function reduce(callable $callback, $initial = null);

    /**
     * Reduces the collection a single value, iterating from right to left.
     *
     * @param mixed $initial
     *
     * @return mixed
     *
     * @see array_reduce()
     */
    public function reduceRight(callable $callback, $initial = null);

    /**
     * Sorts the collection in-place, using an optional comparator function.
     *
     * @psalm-return static<TKey, T>
     */
    public function sort(?callable $comparator = null): self;

    /**
     * Sorts and returns a copy of the collection using an optional comparator function.
     *
     * @return static
     * @psalm-return static<TKey, T>
     */
    public function sorted(?callable $comparator = null): self;

    /**
     * Returns whether the collection is empty.
     */
    public function isEmpty(): bool;

    /**
     * Returns the collection as an array.
     */
    public function toArray(): array;

    /**
     * Returns a new collection from an array or iterable.
     *
     * @return static
     * @psalm-return static<TKey, T>
     */
    public static function fromArray(iterable $elements, ?string $type = null): self;

    /**
     * Returns a new collection from a JSON string.
     *
     * @return static
     * @psalm-return static<TKey, T>
     */
    public static function fromJson(string $json, ?string $type = null): self;
}
