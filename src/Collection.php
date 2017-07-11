<?php

namespace Palmtree\Collection;

class Collection implements \ArrayAccess, \IteratorAggregate, \Countable, \Serializable
{
    protected $items;
    protected $type;

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

    public function set($key, $item)
    {
        $valid = true;
        if ($this->getType()) {
            if (class_exists($this->getType())) {
                if (!is_a($item, $this->getType())) {
                    $valid = false;
                }
            } else {
                if (is_string($this->getType())) {
                    $type = gettype($item);

                    if (!(isset(static::$typeMap[$type]) && in_array($this->getType(), static::$typeMap[$type]))) {
                        $valid = false;
                    }
                }
            }
        }

        if (!$valid) {
            throw new \InvalidArgumentException(sprintf('Item must be of type %s', $this->getType()));
        }

        $this->items[$key] = $item;

        return $this;
    }

    public function get($key)
    {
        if (!isset($this->items[$key])) {
            throw new \InvalidArgumentException(sprintf("Item '%s' does not exist.", $key));
        }

        return $this->items[$key];
    }

    public function add(array $items)
    {
        foreach ($items as $key => $item) {
            $this->set($key, $item);
        }

        return $this;
    }

    public function remove($key)
    {
        unset($this->items[$key]);

        return $this;
    }

    public function all()
    {
        return $this->items;
    }

    public function first()
    {
        foreach ($this->all() as $item) {
            return $item;
        }

        return null;
    }

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
        return new \ArrayIterator($this->items);
    }

    /**
     * @param null $type
     *
     * @return Collection
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return null
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
}
