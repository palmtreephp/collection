<?php

namespace Palmtree\Collection;

use Palmtree\Collection\Validator\TypeValidator;

abstract class AbstractCollection implements CollectionInterface
{
    /** @var array */
    protected $items = [];
    /** @var TypeValidator */
    protected $validator;

    /**
     * AbstractCollection constructor.
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
    public function get($key)
    {
        return $this->hasKey($key) ? $this->items[$key] : null;
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

    public function remove($key)
    {
        unset($this->items[$key]);
    }

    /**
     * @inheritDoc
     */
    public function getKeys()
    {
        return static::fromArray(array_keys($this->items));
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
    public function count()
    {
        return count($this->items);
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
    public function filter(callable $predicate = null, $keys = false)
    {
        if (is_null($predicate)) {
            $predicate = function ($value) {
                return (bool)$value;
            };
        }

        $filtered = [];
        foreach ($this->items as $key => $value) {
            $args = [$value];
            if ($keys) {
                $args[] = $key;
            }
            if ($predicate(...$args)) {
                $filtered[$key] = $value;
            }
        }

        return self::fromArray($filtered, $this->validator->getType());
    }

    /**
     * @inheritDoc
     */
    public function map(callable $callback, $keys = false)
    {
        $map = [];
        foreach ($this->items as $key => $value) {
            $args = [$value];

            if ($keys) {
                $args[] = $key;
            }

            $map[$key] = $callback(...$args);
        }

        return static::fromArray($map, $this->getValidator()->getType());
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
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return $this->items;
    }

    /**
     * @inheritDoc
     */
    public static function fromJson($json, $type = null)
    {
        return static::fromArray(json_decode($json, true), $type);
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
}
