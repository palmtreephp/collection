<?php

namespace Palmtree\Collection;

class Sequence extends AbstractCollection
{
    /**
     * @inheritDoc
     */
    public function add(iterable $elements): CollectionInterface
    {
        return $this->push(...$elements);
    }

    /**
     * Pushes an item on to the end of the sequence.
     *
     * @param $elements ...
     *
     * @return Sequence
     */
    public function push(...$elements): CollectionInterface
    {
        foreach ($elements as $element) {
            $this->validate($element);

            $this->elements[] = $element;
        }

        return $this;
    }

    /**
     * Pops the item off the end of sequence.
     *
     * @return mixed
     */
    public function pop()
    {
        return array_pop($this->elements);
    }

    /**
     * Shifts an item off the beginning of sequence.
     *
     * @return mixed
     */
    public function shift()
    {
        return array_shift($this->elements);
    }

    /**
     * Prepends one or more items to the beginning of the sequence
     *
     * @param mixed $elements ...
     *
     * @return int
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
