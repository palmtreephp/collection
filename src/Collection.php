<?php

namespace Palmtree\Collection;

class Collection implements CollectionInterface
{
    /** @var array */
    protected $items;
    /** @var TypeValidator */
    protected $validator;

    /**
     * Collection constructor.
     *
     * @param string $type
     */
    public function __construct($type = null)
    {
        $this->validator = new TypeValidator($type);
    }

    /**
     * Adds a single item with the given key to the collection.
     *
     * @param mixed $key
     * @param mixed $item
     *
     * @return $this
     */
    public function set($key, $item)
    {
        $this->validator->validate($item);

        if (is_null($key)) {
            $this->items[] = $item;
        } else {
            $this->items[$key] = $item;
        }

        return $this;
    }

    /**
     * Returns a single item with the given key from the collection.
     *
     * @param mixed $key
     *
     * @return mixed
     */
    public function get($key)
    {
        return $this->items[$key];
    }

    /**
     * Pushes a single item on to the end of the collection.
     *
     * @param mixed $item
     *
     * @return $this
     */
    public function push($item)
    {
        return $this->set(null, $item);
    }

    /**
     * Adds a set of items to the collection.
     *
     * @param array|\Traversable $items
     *
     * @return $this
     */
    public function add($items)
    {
        foreach ($items as $key => $item) {
            $this->set($key, $item);
        }

        return $this;
    }

    /**
     * Removes an item with the given key from the collection.
     *
     * @param mixed $key
     *
     * @return $this
     */
    public function remove($key)
    {
        unset($this->items[$key]);

        return $this;
    }

    /**
     * Removes the given item from the collection if it is found.
     *
     * @param mixed $item
     *
     * @return $this
     */
    public function removeItem($item)
    {
        $key = array_search($item, $this->items);

        if ($key !== false) {
            $this->remove($key);
        }

        return $this;
    }

    /**
     * Clears all items from the collection.
     */
    public function clear()
    {
        $this->items = [];

        return $this;
    }

    /**
     * Returns the entire collection.
     *
     * @return array
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * Returns the first item in the collection.
     *
     * @return mixed
     */
    public function first()
    {
        return reset($this->items);
    }

    /**
     * Returns the last item in the collection.
     *
     * @return mixed
     */
    public function last()
    {
        return end($this->items);
    }

    /**
     * Returns whether the given item is in the collection.
     *
     * @param mixed $item
     * @param bool  $strict
     *
     * @return bool
     */
    public function contains($item, $strict = true)
    {
        return in_array($item, $this->items, $strict);
    }

    /**
     * Returns whether the given key exists in the collection.
     *
     * @param $key
     *
     * @return bool
     */
    public function containsKey($key)
    {
        return isset($this->items[$key]) || array_key_exists($key, $this->items);
    }

    /**
     * {@inheritDoc}
     */
    public function getKeys()
    {
        return array_keys($this->items);
    }

    /**
     * {@inheritDoc}
     */
    public function getValues()
    {
        return array_values($this->items);
    }

    public function map(callable $callback)
    {
        return self::fromArray(array_map($callback, $this->items), $this->getValidator()->getType());
    }

    /**
     * Returns a new instance containing items in the collection filtered by a predicate.
     *
     * @param callable $filter
     * @param int      $flags
     *
     * @return Collection
     */
    public function filter(callable $filter = null, $flags = 0)
    {
        return self::fromArray(array_filter($this->items, $filter, $flags), $this->validator->getType());
    }

    /**
     * @param array|\Traversable $items
     * @param string             $type
     *
     * @return Collection
     */
    public static function fromArray($items, $type = null)
    {
        $collection = new Collection($type);
        $collection->add($items);

        return $collection;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->items;
    }

    /**
     * @return TypeValidator
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset)
    {
        return $this->containsKey($offset);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        if (!isset($offset)) {
            $this->push($value);
        } else {
            $this->set($offset, $value);
        }
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }

    /**
     * @inheritDoc
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * @inheritDoc
     */
    public function serialize()
    {
        return serialize(get_object_vars($this));
    }

    /**
     * @inheritDoc
     */
    public function unserialize($serialized)
    {
        foreach (unserialize($serialized) as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}
