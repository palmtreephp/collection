<?php

namespace Palmtree\Collection;

class Sequence extends AbstractCollection
{
    /**
     * Pushes an item on to the end of the sequence.
     *
     * @param $item
     *
     * @return Sequence
     */
    public function push($item)
    {
        $this->validator->validate($item);

        $this->items[] = $item;

        return $this;
    }

    /**
     * Pops the item off the end of sequence.
     *
     * @return mixed
     */
    public function pop()
    {
        return array_pop($this->items);
    }

    /**
     * Shifts an item off the beginning of sequence.
     *
     * @return mixed
     */
    public function shift()
    {
        return array_shift($this->items);
    }

    /**
     * Prepends one or more items to the beginning of an array
     *
     * @param mixed $item
     *
     * @return int
     */
    public function unshift(...$item)
    {
        $this->validator->validate($item);

        return array_unshift($this->items, $item);
    }

    /**
     * @inheritDoc
     */
    public function add($items)
    {
        foreach ($items as $item) {
            $this->push($item);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        // TODO: should we throw an exception here?
        $this->validator->validate($value);

        $this->items[$offset] = $value;
    }
}
