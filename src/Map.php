<?php

namespace Palmtree\Collection;

class Map extends AbstractCollection
{
    /**
     * {@inheritDoc}
     *
     * @return self
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
     * @param mixed $element
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
     * @return self
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
     * @param mixed $value
     * @throws Exception\InvalidTypeException
     */
    public function offsetSet($offset, $value): void
    {
        $this->set($offset, $value);
    }
}
