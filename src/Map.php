<?php

namespace Palmtree\Collection;

class Map extends AbstractCollection
{

    /**
     * @inheritDoc

     * @return Map
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
     * @param string|int $key
     * @param mixed      $element
     *
     * @return Map
     */
    public function set($key, $element): Map
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
