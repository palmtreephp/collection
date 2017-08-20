<?php

namespace Palmtree\Collection;

class Collection implements \ArrayAccess, \IteratorAggregate, \Countable, \Serializable
{
    /** @var mixed */
    protected $items;
    /** @var mixed */
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

    public function __construct($items = [], $type = null)
    {
        $this
            ->add($items)
            ->setType($type);
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
        if (!$this->validateType($item)) {
            throw new \InvalidArgumentException(sprintf('Item must be of type %s', $this->getType()));
        }

        $this->items[$key] = $item;

        return $this;
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
        if (!$this->validateType($item)) {
            throw new \InvalidArgumentException(sprintf('Item must be of type %s', $this->getType()));
        }

        $this->items[] = $item;

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
        if (!isset($this->items[$key])) {
            throw new \InvalidArgumentException(sprintf("Item '%s' does not exist.", $key));
        }

        return $this->items[$key];
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
     * Returns the entire collection.
     *
     * @return array|\Traversable|\ArrayAccess
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
        foreach ($this->all() as $item) {
            return $item;
        }

        return null;
    }

    /**
     * Returns the last item in the collection.
     *
     * @return mixed
     */
    public function last()
    {
        $items = $this->all();

        return ($items) ? array_slice($items, -1)[0] : null;
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset)
    {
        return isset($this->items[$offset]);
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
        $this->set($offset, $value);
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
        return new \ArrayIterator($this->all());
    }

    /**
     * Sets the type all items in the collection must be.
     *
     * Can be a primitive type or class name.
     *
     * @see $typeMap for valid primitive types.
     *
     * @param mixed $type
     *
     * @return Collection
     */
    public function setType($type)
    {
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
     * @inheritDoc
     */
    public function count()
    {
        return count($this->all());
    }

    /**
     * @inheritDoc
     */
    public function serialize()
    {
        return serialize($this->all());
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
     * @return bool
     */
    protected function validateType($item)
    {
        if (!$this->getType()) {
            return true;
        }

        if (class_exists($this->getType()) || interface_exists($this->getType())) {
            if (!is_a($item, $this->getType())) {
                return false;
            }
        } elseif (is_string($this->getType())) {
            $type = gettype($item);

            if (!(isset(static::$typeMap[$type]) && in_array($this->getType(), static::$typeMap[$type]))) {
                return false;
            }
        }

        return true;
    }
}
