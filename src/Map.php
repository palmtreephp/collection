<?php

namespace Palmtree\Collection;

class Map extends AbstractCollection
{
    /**
     * @inheritDoc
     */
    public function add($elements)
    {
        foreach ($elements as $key => $element) {
            $this->set($key, $element);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getValues()
    {
        return static::fromArray(array_values($this->elements), $this->getValidator()->getType());
    }

    /**
     * Adds a single element with the given key to the collection.
     *
     * @param string|int $key
     * @param mixed      $element
     * @return Map
     */
    public function set($key, $element)
    {
        $this->validate($element);

        $this->elements[$key] = $element;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }
}
