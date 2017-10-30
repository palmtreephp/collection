<?php

namespace Palmtree\Collection;

use Palmtree\Collection\Exception\InvalidTypeException;

class Collection implements \ArrayAccess, \IteratorAggregate, \Countable, \Serializable
{
    /** @var array */
    protected $items;
    /** @var string */
    protected $type;

    /**
     * An array of primitive types which $this->type can be set to.
     * The keys are values returned by gettype() and the values are arrays
     * of aliases for that type which can be passed to $this->setType().
     *
     * @var array
     */
    public static $typeMap = [
        'boolean'  => ['boolean', 'bool'],
        'integer'  => ['integer', 'int'],
        'double'   => ['double', 'float'],
        'string'   => ['string'],
        'array'    => ['array'],
        'object'   => ['object'],
        'resource' => ['resource'],
    ];

    /**
     * Collection constructor.
     *
     * @param array|\Traversable $items
     * @param string             $type
     */
    public function __construct($items = [], $type = null)
    {
        $this
            ->setType($type)
            ->add($items);
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
        try {
            $this->validateType($item);

            if (is_null($key)) {
                $this->items[] = $item;
            } else {
                $this->items[$key] = $item;
            }
        } catch (\InvalidArgumentException $e) {
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
    public function add(array $items)
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
        $key = array_search($item, $this->all());

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
     *
     * @return bool
     */
    public function contains($item)
    {
        return in_array($item, $this->items, true);
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
     * Returns a new instance containing items in the collection filtered by a predicate.
     *
     * @param \Closure $filter
     *
     * @return Collection
     */
    public function filter(\Closure $filter)
    {
        return new static(array_filter($this->items, $filter), $this->getType());
    }

    /**
     * Sets the type all items in the collection must be. Can be a primitive type or class name.
     *
     * @see $typeMap for valid primitive types.
     *
     * @param mixed $type
     *
     * @return Collection
     */
    public function setType($type)
    {
        if (!is_null($type) && !is_string($type)) {
            throw new \InvalidArgumentException('Type must be a string');
        }

        $this->type = $type;

        return $this;
    }

    /**
     * Returns the type for this collection.
     *
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->items;
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
            return $this->push($value);
        }

        return $this->set($offset, $value);
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
        return serialize($this->items);
    }

    /**
     * @inheritDoc
     */
    public function unserialize($serialized)
    {
        $this->add(unserialize($serialized));
    }

    /**
     * Returns whether the given item is a valid type.
     *
     * @param mixed $item
     *
     * @throws InvalidTypeException
     */
    protected function validateType($item)
    {
        $expected = $this->getType();
        if (!$expected || !is_string($expected)) {
            return;
        }

        $actual = (is_object($item)) ? get_class($item) : gettype($item);

        $valid = false;
        if ((class_exists($expected) || interface_exists($expected)) && $item instanceof $expected) {
            $valid = true;
        } elseif (isset(static::$typeMap[$actual]) && in_array($expected, static::$typeMap[$actual])) {
            $valid = true;
        }

        if (!$valid) {
            throw new InvalidTypeException($expected, $actual);
        }
    }
}
