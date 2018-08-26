<?php

namespace Palmtree\Collection;

class Sequence extends AbstractCollection
{
    /**
     * @inheritDoc
     *
     * @return Sequence
     */
    public function add(iterable $elements): CollectionInterface
    {
        return $this->push(...$elements);
    }

    /**
     * Pushes an element on to the end of the sequence.
     *
     * @param $elements ...
     *
     * @return Sequence
     */
    public function push(...$elements): Sequence
    {
        foreach ($elements as $element) {
            $this->validate($element);

            $this->elements[] = $element;
        }

        return $this;
    }

    /**
     * Pops the last element off the end of sequence.
     *
     * @return mixed
     */
    public function pop()
    {
        return array_pop($this->elements);
    }

    /**
     * Shifts an element off the beginning of sequence.
     *
     * @return mixed
     */
    public function shift()
    {
        return array_shift($this->elements);
    }

    /**
     * Prepends one or more elements to the beginning of the sequence.
     *
     * @param mixed $elements ...
     *
     * @return int The number of new elements in the sequence.
     */
    public function unshift(...$elements)
    {
        foreach ($elements as $element) {
            $this->validate($element);
        }

        return array_unshift($this->elements, ...$elements);
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        $this->validate($value);

        if (is_null($offset)) {
            $this->elements[] = $value;
        } else {
            $this->elements[$offset] = $value;
        }
    }
}
