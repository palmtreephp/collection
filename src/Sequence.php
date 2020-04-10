<?php

namespace Palmtree\Collection;

use Palmtree\Collection\Exception\BadMethodCallException;

class Sequence extends AbstractCollection
{
    /**
     * {@inheritDoc}
     *
     * @return self
     */
    public function add(iterable $elements): CollectionInterface
    {
        return $this->push(...$elements);
    }

    /**
     * Pushes one or more elements on to the end of the sequence.
     *
     * @param mixed ...$elements
     */
    public function push(...$elements): self
    {
        foreach ($elements as $element) {
            $this->validator->validate($element);

            $this->elements[] = $element;
        }

        $this->reindex();

        return $this;
    }

    /**
     * Pops and returns the last element of the sequence, shortening the sequence by one element.
     *
     * @return mixed
     *
     * @see array_pop
     */
    public function pop()
    {
        $popped = array_pop($this->elements);

        $this->reindex();

        return $popped;
    }

    /**
     * Shifts an element off the beginning of sequence and returns it.
     *
     * @return mixed
     *
     * @see array_shift
     */
    public function shift()
    {
        $shifted = array_shift($this->elements);

        $this->reindex();

        return $shifted;
    }

    /**
     * Prepends one or more elements to the beginning of the sequence.
     *
     * @param mixed ...$elements
     *
     * @see array_unshift
     */
    public function unshift(...$elements): int
    {
        foreach ($elements as $element) {
            $this->validator->validate($element);
        }

        $result = array_unshift($this->elements, ...$elements);

        $this->reindex();

        return $result;
    }

    /**
     * {@inheritDoc}
     *
     * @return self
     */
    public function sort(?callable $comparator = null): CollectionInterface
    {
        if (!$comparator) {
            sort($this->elements);

            return $this;
        }

        usort($this->elements, $comparator);

        $this->reindex();

        return $this;
    }

    private function reindex(): void
    {
        if (empty($this->indexes)) {
            return;
        }

        foreach ($this->indexes as $index) {
            $index->clear();
        }

        foreach ($this->elements as $key => $value) {
            foreach ($this->indexes as $index) {
                $index->add($key, $value);
            }
        }
    }

    /**
     * @param null  $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        if ($offset !== null) {
            throw new BadMethodCallException("Cannot set element at offset $offset. Sequences must be sequential");
        }

        $this->push($value);
    }
}
