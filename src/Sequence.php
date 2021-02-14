<?php

namespace Palmtree\Collection;

use Palmtree\Collection\Exception\BadMethodCallException;
use Palmtree\Collection\Exception\InvalidTypeException;

/**
 * @template T
 * @extends AbstractCollection<int,T>
 */
class Sequence extends AbstractCollection
{
    /**
     * {@inheritDoc}
     *
     * @throws InvalidTypeException
     */
    public function add(iterable $elements): CollectionInterface
    {
        return $this->push(...$elements);
    }

    /**
     * Pushes one or more elements on to the end of the sequence.
     *
     * @param mixed ...$elements
     * @psalm-param T ...$elements
     *
     * @psalm-return static<T>
     *
     * @throws InvalidTypeException
     */
    public function push(...$elements): self
    {
        foreach ($elements as $element) {
            $this->validator->validate($element);

            $this->elements[] = $element;
        }

        $this->reindex();

        return $this;
    }

    /**
     * Pops and returns the last element of the sequence, shortening the sequence by one element.
     *
     * @return mixed
     * @psalm-return T
     *
     * @see array_pop
     */
    public function pop()
    {
        $popped = array_pop($this->elements);

        $this->reindex();

        return $popped;
    }

    /**
     * Shifts an element off the beginning of sequence and returns it.
     *
     * @return mixed
     * @psalm-return T
     *
     * @see array_shift
     */
    public function shift()
    {
        $shifted = array_shift($this->elements);

        $this->reindex();

        return $shifted;
    }

    /**
     * Prepends one or more elements to the beginning of the sequence.
     *
     * @param mixed ...$elements
     * @psalm-param T ...$elements
     *
     * @throws InvalidTypeException
     *
     * @see array_unshift
     */
    public function unshift(...$elements): int
    {
        foreach ($elements as $element) {
            $this->validator->validate($element);
        }

        /** @psalm-suppress InvalidPropertyAssignmentValue */
        $result = array_unshift($this->elements, ...$elements);

        $this->reindex();

        return $result;
    }

    /**
     * {@inheritDoc}
     *
     * @psalm-return static<T>
     */
    public function sort(?callable $comparator = null): CollectionInterface
    {
        if (!$comparator) {
            sort($this->elements);

            return $this;
        }

        usort($this->elements, $comparator);

        $this->reindex();

        return $this;
    }

    private function reindex(): void
    {
        if (empty($this->indexes)) {
            return;
        }

        foreach ($this->indexes as $index) {
            $index->clear();
        }

        foreach ($this->elements as $key => $value) {
            foreach ($this->indexes as $index) {
                $index->add((string)$key, $value);
            }
        }
    }

    /**
     * @param int|null $offset
     * @param mixed $value
     * @psalm-param T $value
     *
     * @throws InvalidTypeException
     */
    public function offsetSet($offset, $value): void
    {
        if ($offset !== null) {
            throw new BadMethodCallException("Cannot set element at offset $offset. Sequences must be sequential");
        }

        $this->push($value);
    }
}
