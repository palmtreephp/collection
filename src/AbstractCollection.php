<?php

namespace Palmtree\Collection;

use Palmtree\Collection\Validator\TypeValidator;

abstract class AbstractCollection implements CollectionInterface
{
    /** @var array */
    protected $elements;
    /** @var TypeValidator */
    protected $validator;

    /**
     * AbstractCollection constructor.
     *
     * @param string $type
     */
    public function __construct(?string $type = null)
    {
        $this->elements  = [];
        $this->validator = new TypeValidator($type);
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
    public function has($element, bool $strict = true): bool
    {
        return \in_array($element, $this->elements, $strict);
    }

    /**
     * @inheritDoc
     */
    public function hasKey($key): bool
    {
        return isset($this->elements[$key]) || \array_key_exists($key, $this->elements);
    }

    /**
     * @inheritDoc
     */
    public function remove($key): CollectionInterface
    {
        unset($this->elements[$key]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeElement($element): CollectionInterface
    {
        $key = \array_search($element, $this->elements, true);

        if ($key !== false) {
            $this->remove($key);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function keys(): CollectionInterface
    {
        return static::fromArray(\array_keys($this->elements));
    }

    /**
     * @inheritDoc
     */
    public function values(): CollectionInterface
    {
        return static::fromArray(\array_values($this->elements), $this->getValidator()->getType());
    }

    /**
     * @inheritDoc
     */
    public function clear(): CollectionInterface
    {
        $this->elements = [];

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        return $this->toArray();
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return $this->elements;
    }

    /**
     * @inheritDoc
     */
    public function first()
    {
        return \reset($this->elements);
    }

    /**
     * @inheritDoc
     */
    public function last()
    {
        return \end($this->elements);
    }

    /**
     * @inheritDoc
     */
    public function count()
    {
        return \count($this->elements);
    }

    /**
     * @inheritDoc
     */
    public function isEmpty(): bool
    {
        return empty($this->elements);
    }

    /**
     * @inheritDoc
     */
    public function filter(callable $predicate = null, bool $keys = false): CollectionInterface
    {
        if (null === $predicate) {
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
    public function map(callable $callback, string $type = null, bool $keys = false): CollectionInterface
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
    public function getValidator(): TypeValidator
    {
        return $this->validator;
    }

    /**
     * @param $element
     *
     * @return bool
     */
    public function validate($element): bool
    {
        return $this->getValidator()->validate($element);
    }

    /**
     * @inheritDoc
     */
    public function getIterator(): \ArrayIterator
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

    /**
     * @inheritDoc
     */
    public static function fromJson(string $json, string $type = null): CollectionInterface
    {
        return static::fromArray(\json_decode($json, true), $type);
    }

    /**
     * @inheritDoc
     */
    public static function fromArray(iterable $elements, string $type = null): CollectionInterface
    {
        $collection = new static($type);
        $collection->add($elements);

        return $collection;
    }
}
