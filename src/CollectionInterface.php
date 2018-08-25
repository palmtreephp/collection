<?php

namespace Palmtree\Collection;

interface CollectionInterface extends \ArrayAccess, \IteratorAggregate, \Countable, \JsonSerializable
{
    /**
     * Returns a single item with the given key from the collection.
     *
     * @param string|int $key
     *
     * @return mixed
     */
    public function get($key);

    /**
     * Adds a set of items to the collection.
     *
     * @param array|\Traversable $elements
     *
     * @return CollectionInterface
     */
    public function add(iterable $elements): CollectionInterface;

    /**
     * Removes an item with the given key from the collection.
     *
     * @param string|int $key
     *
     * @return CollectionInterface
     */
    public function removeItem($key): CollectionInterface;

    /**
     * Clears all items from the collection.
     *
     * @return CollectionInterface
     */
    public function clear(): CollectionInterface;

    /**
     * Returns the entire collection.
     *
     * @return array
     */
    public function all(): array;

    /**
     * Returns the first item in the collection.
     *
     * @return mixed
     */
    public function first();

    /**
     * Returns the last item in the collection.
     *
     * @return mixed
     */
    public function last();

    /**
     * Returns whether the given item is in the collection.
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
     * @return bool
     */
    public function hasKey($key): bool;

    /**
     * Returns whether the collection is empty.
     *
     * @return bool
     */
    public function isEmpty(): bool;

    /**
     * @return CollectionInterface
     */
    public function getKeys(): CollectionInterface;

    /**
     * Returns a new instance containing items mapped from the given callback.
     *
     * @param callable    $callback
     * @param string|null $type Type of the mapped collection
     * @param bool        $keys Whether to pass keys as a second argument to the callback.
     *
     * @return CollectionInterface
     */
    public function map(callable $callback, string $type = null, bool $keys = false): CollectionInterface;

    /**
     * Returns a new instance containing items in the collection filtered by a predicate.
     *
     * @param callable $predicate
     * @param bool     $keys Whether to pass keys as a second argument to the predicate
     *
     * @return CollectionInterface
     */
    public function filter(callable $predicate = null, bool $keys = false): CollectionInterface;

    /**
     * @param array|\Traversable $elements
     * @param string             $type
     *
     * @return CollectionInterface
     */
    public static function fromArray(iterable $elements, string $type = null): CollectionInterface;

    /**
     * @return array
     */
    public function toArray(): array;

    /**
     * @param string $json
     * @param string $type
     * @return CollectionInterface
     */
    public static function fromJson(string $json, string $type = null): CollectionInterface;
}
