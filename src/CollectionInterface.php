<?php

namespace Palmtree\Collection;

interface CollectionInterface extends \ArrayAccess, \IteratorAggregate, \Countable, \Serializable
{
    /**
     * Adds a single item with the given key to the collection.
     *
     * @param string|int $key
     * @param mixed      $value
     * @return CollectionInterface
     */
    public function set($key, $value);

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
     * @param array|\Traversable $items
     *
     * @return CollectionInterface
     */
    public function add($items);

    /**
     * Removes an item with the given key from the collection.
     *
     * @param string|int $key
     *
     * @return CollectionInterface
     */
    public function remove($key);

    /**
     * Removes the given item from the collection if it is found.
     *
     * @param mixed $item
     *
     * @return CollectionInterface
     */
    public function removeItem($item);

    /**
     * Clears all items from the collection.
     *
     * @return CollectionInterface
     */
    public function clear();

    /**
     * Returns the entire collection.
     *
     * @return array
     */
    public function all();

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
     * @deprecated Use has() instead
     * Returns whether the given item is in the collection.
     *
     * @param mixed $item
     * @param bool  $strict
     *
     * @return bool
     */
    public function contains($item, $strict = true);

    /**
     * Returns whether the given item is in the collection.
     *
     * @param mixed $item
     * @param bool  $strict
     *
     * @return bool
     */
    public function has($item, $strict = true);

    /**
     * @deprecated Use hasKey instead
     *
     * Returns whether the given key exists in the collection.
     *
     * @param string|int $key
     *
     * @return bool
     */
    public function containsKey($key);

    /**
     * Returns whether the given key exists in the collection.
     *
     * @param string|int $key
     *
     * @return bool
     */
    public function hasKey($key);

    /**
     * Returns whether the collection is empty.
     *
     * @return bool
     */
    public function isEmpty();

    /**
     * @return CollectionInterface
     */
    public function getKeys();

    /**
     * @return CollectionInterface
     */
    public function getValues();

    /**
     * Returns a new instance containing items mapped from the given callback.
     *
     * @param callable $callback
     *
     * @return CollectionInterface
     */
    public function map(callable $callback);

    /**
     * Returns a new instance containing items in the collection filtered by a predicate.
     *
     * @param callable $predicate
     *
     * @return CollectionInterface
     */
    public function filter(callable $predicate = null);

    /**
     * @param array|\Traversable $items
     * @param string             $type
     *
     * @return CollectionInterface
     */
    public static function fromArray($items, $type = null);

    /**
     * @return array
     */
    public function toArray();
}
