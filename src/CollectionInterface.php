<?php

namespace Palmtree\Collection;

interface CollectionInterface extends \ArrayAccess, \IteratorAggregate, \Countable, \JsonSerializable
{
    /**
     * Returns a single element with the given key from the collection.
     *
     * @param string|int $key
     *
     * @return mixed
     */
    public function get($key);

    /**
     * Adds a set of elements to the collection.
     *
     * @param iterable $elements
     *
     * @return CollectionInterface
     */
    public function add(iterable $elements): self;

    /**
     * Removes an element with the given key from the collection.
     *
     * @param string|int $key
     *
     * @return CollectionInterface
     */
    public function remove($key): self;

    /**
     * Removes an element from the collection.
     *
     * @param mixed $element
     *
     * @return CollectionInterface
     */
    public function removeElement($element): self;

    /**
     * Clears all elements from the collection.
     *
     * @return CollectionInterface
     */
    public function clear(): self;

    /**
     * Returns the entire collection as an array.
     *
     * @return array
     */
    public function all(): array;

    /**
     * Returns the first element in the collection.
     *
     * @return mixed
     */
    public function first();

    /**
     * Returns the last element in the collection.
     *
     * @return mixed
     */
    public function last();

    /**
     * Returns a new collection containing this collection's keys.
     *
     * @return CollectionInterface
     */
    public function keys(): self;

    /**
     * Returns a new collection containing this collection's values.
     *
     * @return CollectionInterface
     */
    public function values(): self;

    /**
     * Returns whether the given element is in the collection.
     *
     * @param mixed $element
     * @param bool  $strict
     *
     * @return bool
     */
    public function has($element, bool $strict = true): bool;

    /**
     * Returns whether the given key exists in the collection.
     *
     * @param string|int
     *
     * @return bool
     */
    public function hasKey($key): bool;

    /**
     * Returns a new instance containing elements mapped from the given callback.
     *
     * @param callable    $callback
     * @param string|null $type     Type of the mapped collection
     * @param bool        $keys     Whether to pass keys as a second argument to the callback.
     *
     * @return CollectionInterface
     */
    public function map(callable $callback, string $type = null, bool $keys = false): self;

    /**
     * Returns a new instance containing elements in the collection filtered by a predicate.
     *
     * @param callable $predicate
     * @param bool     $keys      Whether to pass keys as a second argument to the predicate
     *
     * @return CollectionInterface
     */
    public function filter(callable $predicate = null, bool $keys = false): self;

    /**
     * Returns whether the collection is empty.
     *
     * @return bool
     */
    public function isEmpty(): bool;

    /**
     * Returns the collection as an array.
     *
     * @return array
     */
    public function toArray(): array;

    /**
     * @param iterable $elements
     * @param string   $type
     *
     * @return CollectionInterface
     */
    public static function fromArray(iterable $elements, string $type = null): self;

    /**
     * @param string $json
     * @param string $type
     *
     * @return CollectionInterface
     */
    public static function fromJson(string $json, string $type = null): self;
}
