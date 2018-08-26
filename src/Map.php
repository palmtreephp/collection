<?php

namespace Palmtree\Collection;

use Palmtree\Collection\Exception\InvalidMapIndex;

class Map extends AbstractCollection
{
    /** @var MapIndex[] */
    private $indices = [];

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

        foreach ($this->indices as $index) {
            $index->add($key, $element);
        }

        return $this;
    }

    /**
     * @inheritDoc
     *
     * @return Map
     */
    public function remove($key): CollectionInterface
    {
        foreach ($this->indices as $index) {
            $index->remove($key);
        }

        return parent::remove($key);
    }

    /**
     * @inheritDoc
     *
     * @return Map
     */
    public function clear(): CollectionInterface
    {
        foreach ($this->indices as $index) {
            $index->clear();
        }

        return parent::clear();
    }

    /**
     * @param string $indexId
     * @param        $key
     *
     * @return mixed|null
     * @throws InvalidMapIndex
     */
    public function getBy(string $indexId, $key)
    {
        if (!isset($this->indices[$indexId])) {
            throw new InvalidMapIndex($indexId);
        }

        return $this->get($this->indices[$indexId]->get($key));
    }

    /**
     * @param string   $id
     * @param callable $callback
     *
     * @return Map
     */
    public function addIndex(string $id, callable $callback): Map
    {
        $index = new MapIndex($id, $callback);

        foreach ($this->elements as $key => $element) {
            $index->add($key, $element);
        }

        $this->indices[$id] = $index;

        return $this;
    }

    /**
     * @param string $id
     *
     * @return Map
     */
    public function removeIndex(string $id): Map
    {
        unset($this->indices[$id]);

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
