<?php

namespace Palmtree\Collection;

interface CollectionInterface extends \ArrayAccess, \IteratorAggregate, \Countable, \Serializable
{
    /**
     * Returns a single item with the given key from the collection.
     *
     * @param mixed $key
     *
     * @return mixed
     */
    public function get($key);

    /**
     * @param $key
     * @param mixed $value
     * @return CollectionInterface
     */
    public function set($key, $value);

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
     * @param mixed $key
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
     * Returns whether the given item is in the collection.
     *
     * @param mixed $item
     *
     * @return bool
     */
    public function contains($item, $strict = true);

    /**
     * Returns whether the given key exists in the collection.
     *
     * @param $key
     *
     * @return bool
     */
    public function containsKey($key);

    /**
     * @return array
     */
    public function getKeys();

    /**
     * @return array
     */
    public function getValues();

    /**
     * Returns a new instance containing items in the collection filtered by a predicate.
     *
     * @param callable $filter
     * @param int      $flags
     *
     * @return Collection
     */
    public function filter(callable $filter = null, $flags = 0);

    /**
     * @param array|\Traversable $items
     * @param string             $type
     *
     * @return Collection
     */
    public static function fromArray($items, $type = null);

    /**
     * @return array
     */
    public function toArray();

    /**
     * @return TypeValidator
     */
    public function getValidator();
}
