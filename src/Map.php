<?php declare(strict_types=1);

namespace Palmtree\Collection;

use Palmtree\Collection\Exception\InvalidTypeException;

/**
 * @template TKey of array-key
 * @template T
 */
class Map implements \ArrayAccess, \IteratorAggregate, \Countable, \JsonSerializable
{
    /** @use CollectionTrait<TKey, T> */
    use CollectionTrait;

    /**
     * Adds a set of elements to the collection.
     *
     * @psalm-param iterable<TKey,T> $elements
     *
     * @return $this
     *
     * @throws InvalidTypeException
     */
    public function add(iterable $elements): self
    {
        foreach ($elements as $key => $element) {
            $this->set($key, $element);
        }

        return $this;
    }

    /**
     * Adds a single element with the given key to the collection.
     *
     * @psalm-param TKey $key
     *
     * @param mixed $element
     * @psalm-param T $element
     *
     * @throws Exception\InvalidTypeException
     */
    public function set(string $key, $element): self
    {
        $this->validator->validate($element);

        $this->elements[$key] = $element;

        foreach ($this->indexes as $index) {
            $index->add($key, $element);
        }

        return $this;
    }

    /**
     * Sorts the collection in-place, using an optional comparator function.
     *
     * @return static
     * @psalm-return static<TKey, T>
     */
    public function sort(?callable $comparator = null): self
    {
        if (!$comparator) {
            asort($this->elements);

            return $this;
        }

        uasort($this->elements, $comparator);

        return $this;
    }

    /**
     * @psalm-param TKey $offset
     *
     * @param mixed $value
     * @psalm-param T $value
     *
     * @throws InvalidTypeException
     */
    public function offsetSet($offset, $value): void
    {
        $this->set($offset, $value);
    }

    /**
     * Returns a new collection from an array or iterable.
     *
     * @template K as string
     * @template V
     * @psalm-param iterable<K, V> $elements
     *
     * @return static
     * @psalm-return static<K, V>
     *
     * @throws InvalidTypeException
     */
    public static function fromArray(iterable $elements, ?string $type = null): self
    {
        return (new static($type))->add($elements);
    }

    /**
     * Returns a new collection from a JSON string.
     *
     * @return static
     *
     * @throws InvalidTypeException
     */
    public static function fromJson(string $json, ?string $type = null): self
    {
        return static::fromArray(json_decode($json, true), $type);
    }
}
