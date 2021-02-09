<?php

namespace Palmtree\Collection;

use Palmtree\Collection\Exception\InvalidIndex;
use Palmtree\Collection\Exception\OutOfBoundsException;
use Palmtree\Collection\Validator\TypeValidator;

abstract class AbstractCollection implements CollectionInterface
{
    /** @var array */
    protected $elements;
    /** @var TypeValidator */
    protected $validator;
    /** @var Index[] */
    protected $indexes = [];

    final public function __construct(?string $type = null)
    {
        $this->elements  = [];
        $this->validator = new TypeValidator($type);
    }

    /**
     * {@inheritDoc}
     */
    public function get($key)
    {
        if (!$this->containsKey($key)) {
            throw new OutOfBoundsException("Element with key '$key' does not exist");
        }

        return $this->elements[$key];
    }

    /**
     * {@inheritDoc}
     */
    public function contains($element, bool $strict = true): bool
    {
        return \in_array($element, $this->elements, $strict);
    }

    /**
     * {@inheritDoc}
     */
    public function containsKey($key): bool
    {
        return isset($this->elements[$key]) || \array_key_exists($key, $this->elements);
    }

    /**
     * {@inheritDoc}
     */
    public function remove($key): CollectionInterface
    {
        foreach ($this->indexes as $index) {
            $index->remove((string)$key);
        }

        unset($this->elements[$key]);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function removeElement($element): CollectionInterface
    {
        $key = array_search($element, $this->elements, true);

        if ($key !== false) {
            $this->remove($key);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function keys(): CollectionInterface
    {
        return static::fromArray(array_keys($this->elements));
    }

    public function values(): CollectionInterface
    {
        return static::fromArray(array_values($this->elements), $this->validator->getType());
    }

    /**
     * {@inheritDoc}
     */
    public function clear(): CollectionInterface
    {
        foreach ($this->indexes as $index) {
            $index->clear();
        }

        $this->elements = [];

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function all(): array
    {
        return $this->toArray();
    }

    /**
     * {@inheritDoc}
     */
    public function first()
    {
        foreach ($this->elements as $element) {
            return $element;
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function last()
    {
        foreach (\array_slice($this->elements, -1) as $element) {
            return $element;
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function firstKey()
    {
        if (\function_exists('array_key_first')) {
            return array_key_first($this->elements);
        }

        foreach ($this->elements as $key => $noop) {
            return $key;
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function lastKey()
    {
        if (\function_exists('array_key_last')) {
            return array_key_last($this->elements);
        }

        return key(\array_slice($this->elements, -1, 1, true));
    }

    /**
     * Returns the total number of elements in the collection.
     */
    public function count(): int
    {
        return \count($this->elements);
    }

    /**
     * {@inheritDoc}
     */
    public function isEmpty(): bool
    {
        return empty($this->elements);
    }

    /**
     * {@inheritDoc}
     */
    public function filter(?callable $predicate = null): CollectionInterface
    {
        if (!$predicate) {
            return static::fromArray(array_filter($this->elements), $this->validator->getType());
        }

        return static::fromArray(array_filter($this->elements, $predicate, \ARRAY_FILTER_USE_BOTH), $this->validator->getType());
    }

    /**
     * {@inheritDoc}
     */
    public function map(callable $callback, ?string $type = null): CollectionInterface
    {
        $map = [];
        foreach ($this->elements as $key => $value) {
            $map[$key] = $callback($value, $key);
        }

        return static::fromArray($map, $type);
    }

    /**
     * {@inheritDoc}
     */
    public function some(callable $predicate): bool
    {
        foreach ($this->elements as $key => $value) {
            if ($predicate($value, $key)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function every(callable $predicate): bool
    {
        foreach ($this->elements as $key => $value) {
            if (!$predicate($value, $key)) {
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function find(callable $predicate)
    {
        foreach ($this->elements as $key => $value) {
            if ($predicate($value, $key)) {
                return $value;
            }
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function reduce(callable $callback, $initial = null)
    {
        return array_reduce($this->elements, $callback, $initial);
    }

    /**
     * {@inheritDoc}
     */
    public function reduceRight(callable $callback, $initial = null)
    {
        return array_reduce(array_reverse($this->elements), $callback, $initial);
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return $this->elements;
    }

    /**
     * @return mixed|null
     *
     * @throws InvalidIndex
     */
    public function getBy(string $indexId, string $key)
    {
        if (!isset($this->indexes[$indexId])) {
            throw new InvalidIndex($indexId);
        }

        return $this->get($this->indexes[$indexId]->get($key));
    }

    public function addIndex(string $id, callable $callback): self
    {
        $index = new Index($callback);

        foreach ($this->elements as $key => $element) {
            $index->add($key, $element);
        }

        $this->indexes[$id] = $index;

        return $this;
    }

    public function removeIndex(string $id): self
    {
        unset($this->indexes[$id]);

        return $this;
    }

    public function getValidator(): TypeValidator
    {
        return $this->validator;
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->elements);
    }

    /**
     * @param string|int $offset
     */
    public function offsetExists($offset): bool
    {
        return $this->containsKey($offset);
    }

    /**
     * @param string|int $offset
     *
     * @return mixed|null
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @param string|int $offset
     */
    public function offsetUnset($offset): void
    {
        $this->remove($offset);
    }

    public function jsonSerialize(): array
    {
        return $this->elements;
    }

    public static function fromJson(string $json, ?string $type = null): CollectionInterface
    {
        return static::fromArray(json_decode($json, true), $type);
    }

    public static function fromArray(iterable $elements, ?string $type = null): CollectionInterface
    {
        $collection = new static($type);
        $collection->add($elements);

        return $collection;
    }
}
