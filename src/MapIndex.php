<?php

namespace Palmtree\Collection;

class MapIndex
{
    /** @var callable */
    private $callback;
    /** @var array */
    private $index;

    /**
     * MapIndex constructor.
     *
     * @param callable $callback
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
        $this->index    = [];
    }

    /**
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->index[$key] ?? null;
    }

    /**
     * @param $key
     *
     * @return MapIndex
     */
    public function remove($key): MapIndex
    {
        unset($this->index[$key]);

        return $this;
    }

    /**
     * @return MapIndex
     */
    public function clear(): MapIndex
    {
        $this->index = [];

        return $this;
    }

    /**
     * @param $key
     * @param $element
     * @return MapIndex
     */
    public function add($key, $element): MapIndex
    {
        $callback = $this->callback;

        $this->index[$callback($element)] = $key;

        return $this;
    }
}
