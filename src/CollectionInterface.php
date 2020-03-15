<?php

namespace Palmtree\Collection;

interface CollectionInterface extends \ArrayAccess, \IteratorAggregate, \Countable, \JsonSerializable
{
    /**
     * Returns a single element with the given key from the collection.
     *
     * @param string|int $key
     */
    public function get($key);

    /**
     * Adds a set of elements to the collection.
     */
    public function add(iterable $elements): self;

    /**
     * Removes an element with the given key from the collection.
     *
     * @param string|int $key
     */
    public function remove($key): self;

    /**
     * Removes an element from the collection.
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
     */
    public function first();

    /**
     * Returns the last element in the collection.
     *
     * @return mixed|null
     */
    public function last();

    /**
     * Returns the first key in the collection.
     *
     * @return string|int|null
     */
    public function firstKey();

    /**
     * Returns the last key in the collection.
     *
     * @return string|int|null
     */
    public function lastKey();

    /**
     * Returns a new collection containing this collection's keys.
     */
    public function keys(): self;

    /**
     * Returns a new collection containing this collection's values.
     */
    public function values(): self;

    /**
     * Returns whether the given element is in the collection.
     */
    public function has($element, bool $strict = true): bool;

    /**
     * Returns whether the given key exists in the collection.
     *
     * @param string|int
     */
    public function hasKey($key): bool;

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
     */
    public function find(callable $predicate);

    /**
     * Reduces the collection a single value.
     *
     * @see array_reduce()
     */
    public function reduce(callable $callback, $initial = null);

    /**
     * Reduces the collection a single value, iterating from right to left.
     *
     * @see array_reduce()
     */
    public function reduceRight(callable $callback, $initial = null);

    /**
     * Returns whether the collection is empty.
     */
    public function isEmpty(): bool;

    /**
     * Returns the collection as an array.
     */
    public function toArray(): array;

    public static function fromArray(iterable $elements, ?string $type = null): self;

    public static function fromJson(string $json, ?string $type = null): self;
}
