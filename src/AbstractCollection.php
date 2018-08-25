<?php

namespace Palmtree\Collection;

use Palmtree\Collection\Validator\TypeValidator;

abstract class AbstractCollection implements CollectionInterface
{
    /** @var array */
    protected $elements = [];
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
    public static function fromJson($json, $type = null)
    {
        return static::fromArray(json_decode($json, true), $type);
    }

    /**
     * @inheritDoc
     */
    public static function fromArray($elements, $type = null)
    {
        $collection = new static($type);
        $collection->add($elements);

        return $collection;
    }

    /**
     * @inheritDoc
     */
    public function get($key)
    {
        return $this->hasKey($key) ? $this->elements[$key] : null;
    }

    /**
     * @inheritDoc
     */
    public function has($element, $strict = true)
    {
        return in_array($element, $this->elements, $strict);
    }

    /**
     * @inheritDoc
     */
    public function hasKey($key)
    {
        return isset($this->elements[$key]) || array_key_exists($key, $this->elements);
    }

    public function remove($key)
    {
        unset($this->elements[$key]);
    }

    /**
     * @inheritDoc
     */
    public function removeItem($element)
    {
        $key = array_search($element, $this->elements);

        if ($key !== false) {
            $this->remove($key);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getKeys()
    {
        return static::fromArray(array_keys($this->elements));
    }

    /**
     * @inheritDoc
     */
    public function clear()
    {
        $this->elements = [];

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function all()
    {
        return $this->toArray();
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return $this->elements;
    }

    /**
     * @inheritDoc
     */
    public function first()
    {
        return reset($this->elements);
    }

    /**
     * @inheritDoc
     */
    public function last()
    {
        return end($this->elements);
    }

    /**
     * @inheritDoc
     */
    public function count()
    {
        return count($this->elements);
    }

    /**
     * @inheritDoc
     */
    public function isEmpty()
    {
        return empty($this->elements);
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
        foreach ($this->elements as $key => $value) {
            $args = [$value];
            if ($keys) {
                $args[] = $key;
            }
            if ($predicate(...$args)) {
                $filtered[$key] = $value;
            }
        }

        return static::fromArray($filtered, $this->getValidator()->getType());
    }

 /**
     * @inheritDoc
     */
    public function map(callable $callback, $type = null, $keys = false)
    {
        $map = [];
        foreach ($this->elements as $key => $value) {
            $args = [$value];

            if ($keys) {
                $args[] = $key;
            }

            $map[$key] = $callback(...$args);
        }

        return static::fromArray($map, $type);
    }

    /**
     * @return TypeValidator
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * @param $element
     * @return bool
     */
    public function validate($element)
    {
        return $this->getValidator()->validate($element);
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->elements);
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
        return $this->elements;
    }
}
