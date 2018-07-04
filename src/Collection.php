<?php

namespace Palmtree\Collection;

use Palmtree\Collection\Validator\TypeValidator;

class Collection implements CollectionInterface
{
    /** @var array */
    protected $items = [];
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
     * @inheritDoc
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
     * @inheritDoc
     */
    public function get($key)
    {
        if (!$this->hasKey($key)) {
            return null;
        }

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
     * @inheritDoc
     */
    public function add($items)
    {
        foreach ($items as $key => $item) {
            $this->set($key, $item);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function remove($key)
    {
        unset($this->items[$key]);

        return $this;
    }

    /**
     * @inheritDoc
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
     * @inheritDoc
     */
    public function clear()
    {
        $this->items = [];

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * @inheritDoc
     */
    public function first()
    {
        return reset($this->items);
    }

    /**
     * @inheritDoc
     */
    public function last()
    {
        return end($this->items);
    }

    /**
     * @inheritDoc
     */
    public function has($item, $strict = true)
    {
        return in_array($item, $this->items, $strict);
    }

    /**
     * @inheritDoc
     */
    public function hasKey($key)
    {
        return isset($this->items[$key]) || array_key_exists($key, $this->items);
    }

    /**
     * @deprecated Use has instead
     */
    public function contains($item, $strict = true)
    {
        trigger_error(__METHOD__ . ' is deprecated and will be removed in v1.0', E_USER_DEPRECATED);

        return $this->has($item, $strict);
    }

    /**
     * @deprecated Use hasKey instead
     */
    public function containsKey($key)
    {
        trigger_error(__METHOD__ . ' is deprecated and will be removed in v1.0', E_USER_DEPRECATED);

        return $this->hasKey($key);
    }

    /**
     * @inheritDoc
     */
    public function isEmpty()
    {
        return empty($this->items);
    }

    /**
     * @inheritDoc
     */
    public function getKeys()
    {
        $keys = [];
        foreach ($this->items as $key => $value) {
            $keys[] = $key;
        }

        return static::fromArray($keys);
    }

    /**
     * @inheritDoc
     */
    public function getValues()
    {
        $values = [];
        foreach ($this->items as $value) {
            $values[] = $value;
        }

        return static::fromArray($values);
    }

    /**
     * @inheritDoc
     */
    public function map(callable $callback)
    {
        $map = [];
        foreach ($this->items as $key => $value) {
            $map[$key] = $callback($value, $key);
        }

        return self::fromArray($map, $this->getValidator()->getType());
    }

    /**
     * @inheritDoc
     */
    public function filter(callable $predicate = null)
    {
        if (is_null($predicate)) {
            $predicate = function ($value) {
                return (bool)$value;
            };
        }

        $filtered = [];
        foreach ($this->items as $key => $value) {
            if ($predicate($value, $key)) {
                $filtered[$key] = $value;
            }
        }

        return self::fromArray($filtered, $this->validator->getType());
    }

    /**
     * @inheritDoc
     */
    public static function fromArray($items, $type = null)
    {
        $collection = new static($type);
        $collection->add($items);

        return $collection;
    }

    /**
     * @inheritDoc
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
        return $this->hasKey($offset);
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
