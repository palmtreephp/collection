<?php

namespace Palmtree\Collection;

/**
 * @template TKey of array-key
 * @template T
 * @extends AbstractCollection<TKey,T>
 */
class Map extends AbstractCollection
{
    /**
     * {@inheritDoc}
     *
     * @return self
     * @psalm-return self<TKey,T>
     *
     * @throws Exception\InvalidTypeException
     */
    public function add(iterable $elements): CollectionInterface
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
     * {@inheritDoc}
     *
     * @psalm-return self<TKey,T>
     */
    public function sort(?callable $comparator = null): CollectionInterface
    {
        if (!$comparator) {
            asort($this->elements);

            return $this;
        }

        uasort($this->elements, $comparator);

        return $this;
    }

    /**
     * @param string $offset
     * @psalm-param TKey $offset
     * @param mixed  $value
     * @psalm-param T $value
     *
     * @throws Exception\InvalidTypeException
     */
    public function offsetSet($offset, $value): void
    {
        $this->set($offset, $value);
    }
}
