<?php

namespace Palmtree\Collection;

class Collection implements \ArrayAccess, \IteratorAggregate, \Countable, \Serializable
{
    protected $items;
    protected $type;

    public function __construct($items = [], $type = null)
    {
        $this
            ->set($items)
            ->setType($type);
    }

    public function add($key, $item)
    {
        if ($this->type && class_exists($this->type) && !$item instanceof $this->type) {
            throw new \InvalidArgumentException(sprintf('Item must be of type %s', $this->type));
        }

        $this->items[$key] = $item;

        return $this;
    }

    public function addMany(array $items)
    {
        foreach ($items as $key => $item) {
            $this->add($key, $item);
        }

        return $this;
    }

    public function remove($key)
    {
        unset($this->items[$key]);

        return $this;
    }

    public function set($items)
    {
        $this->items = [];

        return $this->addMany($items);
    }

    public function all()
    {
        return $this->items;
    }

    public function get($key)
    {
        if (!isset($this->items[$key])) {
            throw new \InvalidArgumentException(sprintf("Item '%s' does not exist.", $key));
        }

        return $this->items[$key];
    }

    public function first()
    {
        $items = $this->all();

        return reset($items);
    }

    public function last()
    {
        $items = $this->all();

        return end($items);
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
        $this->add($offset, $value);
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
        $this->set(unserialize($serialized));
    }
}
