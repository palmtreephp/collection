<?php

namespace Palmtree\Collection;

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
        return array_pop($this->elements);
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
        return array_shift($this->elements);
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

        return array_unshift($this->elements, ...$elements);
    }

    /**
     * @param int|null $offset
     * @param mixed    $value
     */
    public function offsetSet($offset, $value): void
    {
        $this->validator->validate($value);

        if ($offset === null) {
            $this->elements[] = $value;
        } else {
            $this->elements[$offset] = $value;
        }
    }
}
