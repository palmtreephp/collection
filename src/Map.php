<?php

namespace Palmtree\Collection;

use Palmtree\Collection\Exception\InvalidMapIndex;

/**
 * @template TKey of array-key
 * @template T
 * @extends AbstractCollection<TKey,T>
 */
class Map extends AbstractCollection
{
    /** @var MapIndex[] */
    private $indexes = [];

    /**
     * {@inheritDoc}
     *
     * @return self
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
     * @param TKey $key
     * @param T $element
     */
    public function set(string $key, $element): self
    {
        $this->validator->validate($element);

        $this->elements[$key] = $element;

        foreach ($this->indexes as $index) {
            $index->add($key, $element);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @return self
     */
    public function remove($key): CollectionInterface
    {
        foreach ($this->indexes as $index) {
            $index->remove((string)$key);
        }

        return parent::remove($key);
    }

    /**
     * {@inheritDoc}
     *
     * @return self
     */
    public function clear(): CollectionInterface
    {
        foreach ($this->indexes as $index) {
            $index->clear();
        }

        return parent::clear();
    }

    /**
     * {@inheritDoc}
     *
     * @return self
     */
    public function sort(?callable $comparator = null): CollectionInterface
    {
        if (!$comparator) {
            asort($this->elements);

            return $this;
        }

        uasort($this->elements, $comparator);

        return $this;
    }

    /**
     * @return mixed|null
     *
     * @throws InvalidMapIndex
     */
    public function getBy(string $indexId, string $key)
    {
        if (!isset($this->indexes[$indexId])) {
            throw new InvalidMapIndex($indexId);
        }

        return $this->get($this->indexes[$indexId]->get($key));
    }

    public function addIndex(string $id, callable $callback): self
    {
        $index = new MapIndex($callback);

        foreach ($this->elements as $key => $element) {
            $index->add($key, $element);
        }

        $this->indexes[$id] = $index;

        return $this;
    }

    public function removeIndex(string $id): self
    {
        unset($this->indexes[$id]);

        return $this;
    }

    /**
     * @param string $offset
     * @param mixed  $value
     */
    public function offsetSet($offset, $value): void
    {
        $this->set($offset, $value);
    }
}
