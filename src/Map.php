<?php

namespace Palmtree\Collection;

class Map extends AbstractCollection
{
    /**
     * Adds a single item with the given key to the collection.
     *
     * @param string|int $key
     * @param mixed      $item
     * @return Map
     */
    public function set($key, $item)
    {
        $this->validator->validate($item);

        if (is_null($key)) {
            $this->items[] = $item;
        } else {
            $this->items[$key] = $item;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function add($items)
    {
        foreach ($items as $key => $item) {
            $this->set($key, $item);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getValues()
    {
        return static::fromArray(array_values($this->items), $this->getValidator()->getType());
    }


    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }
}
