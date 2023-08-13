<?php

declare(strict_types=1);

namespace Palmtree\EasyCollection;

/**
 * @template TKey of array-key
 * @template T
 *
 * @template-implements \IteratorAggregate<TKey,T>
 * @template-implements \ArrayAccess<TKey,T>
 *
 * @psalm-consistent-constructor
 */
class Collection implements \Countable, \IteratorAggregate, \ArrayAccess, \JsonSerializable
{
    /**
     * @var array<TKey,T>
     */
    private array $elements = [];

    /**
     * @param iterable<TKey,T> $elements
     */
    public function __construct(iterable $elements = [])
    {
        foreach ($elements as $key => $element) {
            $this->set($key, $element);
        }
    }

    /**
     * @template UKey of array-key
     * @template U
     *
     * @param iterable<UKey,U> $elements
     *
     * @return Collection<UKey, U>
     */
    public static function create(iterable $elements = []): self
    {
        return new static($elements);
    }

    /**
     * Returns the element with the given key.
     *
     * @param TKey $key
     *
     * @return T
     */
    public function get(int|string $key): mixed
    {
        return $this->elements[$key];
    }

    /**
     * Sets the given element to the given key in the collection.
     *
     * @param TKey $key
     * @param T    $element
     *
     * @return Collection<TKey, T>
     */
    public function set(int|string $key, mixed $element): self
    {
        $this->elements[$key] = $element;

        return $this;
    }

    /**
     * Adds the given element onto the end of the collection.
     *
     * @param T ...$element
     *
     * @return Collection<TKey,T>
     */
    public function add(mixed ...$element): self
    {
        foreach ($element as $el) {
            /** @psalm-suppress InvalidPropertyAssignmentValue */
            $this->elements[] = $el;
        }

        return $this;
    }

    /**
     * Removes the element with the given key from the collection and returns it.
     *
     * @param TKey $key
     *
     * @return T|null
     */
    public function remove(int|string $key)
    {
        if (!$this->containsKey($key)) {
            return null;
        }

        $removed = $this->elements[$key];

        unset($this->elements[$key]);

        return $removed;
    }

    /**
     * Removes the given element from the collection and returns whether it existed or not.
     *
     * @param T $element
     */
    public function removeElement(mixed $element): bool
    {
        $key = $this->key($element);

        if ($key === false) {
            return false;
        }

        unset($this->elements[$key]);

        return true;
    }

    /**
     * Returns a new collection containing the current collection's keys.
     *
     * @return Collection<int, TKey>
     */
    public function keys(): self
    {
        return new self(array_keys($this->elements));
    }

    /**
     * Returns a new collection containing the current collection's values.
     *
     * @return Collection<int, T>
     */
    public function values(): self
    {
        return new self(array_values($this->elements));
    }

    /**
     * Returns the first key in the collection.
     *
     * @return TKey|null
     */
    public function firstKey(): int|string|null
    {
        return array_key_first($this->elements);
    }

    /**
     * Returns the last key in the collection.
     *
     * @return TKey|null
     */
    public function lastKey(): int|string|null
    {
        return array_key_last($this->elements);
    }

    /**
     * Returns the first element in the collection.
     *
     * @return T|null
     */
    public function first(): mixed
    {
        $firstKey = $this->firstKey();

        if ($firstKey === null) {
            return null;
        }

        return $this->elements[$firstKey];
    }

    /**
     * Returns the last element in the collection.
     *
     * @return T|null
     */
    public function last(): mixed
    {
        $lastKey = $this->lastKey();

        if ($lastKey === null) {
            return null;
        }

        return $this->elements[$lastKey];
    }

    /**
     * Returns the given element's key if it is present within the collection or false otherwise.
     *
     * @param T $element
     *
     * @return TKey|false
     */
    public function key(mixed $element): int|string|false
    {
        return array_search($element, $this->elements, true);
    }

    /**
     * Returns whether the given element exists in the collection.
     *
     * @param T $element
     */
    public function contains(mixed $element): bool
    {
        return \in_array($element, $this->elements, true);
    }

    /**
     * Returns whether the given key exists in the collection.
     *
     * @param TKey $key
     */
    public function containsKey(int|string $key): bool
    {
        return isset($this->elements[$key]) || \array_key_exists($key, $this->elements);
    }

    /**
     * Applies the given callback function to each element in the collection.
     *
     * @psalm-param callable(T, TKey=, int=):bool $callback
     */
    public function each(callable $callback): self
    {
        $loopIndex = 0;
        foreach ($this->elements as $key => $element) {
            $result = $callback($element, $key, $loopIndex++);

            if ($result === false) {
                break;
            }
        }

        return $this;
    }

    /**
     * Returns the first matching element which passes the predicate function.
     *
     * @param callable(T, TKey=):bool $predicate
     *
     * @return T|null
     */
    public function find(callable $predicate): mixed
    {
        foreach ($this->elements as $element) {
            if ($predicate($element)) {
                return $element;
            }
        }

        return null;
    }

    /**
     * Returns a new collection containing all elements which pass the predicate function.
     *
     * @param ?callable(T, TKey=):bool $predicate
     *
     * @return Collection<TKey,T>
     *
     * @psalm-suppress PossiblyNullArgument
     */
    public function filter(?callable $predicate = null): self
    {
        return new self(array_filter($this->elements, $predicate, \ARRAY_FILTER_USE_BOTH));
    }

    /**
     * Returns a new collection whose values are mapped by the callback function.
     *
     * @param callable(T, TKey=):U $callback
     *
     * @return Collection<TKey, U>
     *
     * @template U
     */
    public function map(callable $callback): self
    {
        $mapped = [];

        foreach ($this->elements as $key => $element) {
            $mapped[$key] = $callback($element, $key);
        }

        return new self($mapped);
    }

    /**
     * Reduces the collection a single value.
     *
     * @param callable(mixed, T):mixed $callback
     */
    public function reduce(callable $callback, mixed $initial = null): mixed
    {
        return array_reduce($this->elements, $callback, $initial);
    }

    /**
     * Returns whether at least one element passes the predicate function.
     *
     * @param callable(T, TKey=):bool $predicate
     */
    public function some(callable $predicate): bool
    {
        foreach ($this->elements as $key => $value) {
            if ($predicate($value, $key)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns whether all elements pass the predicate function.
     *
     * @param callable(T, TKey=):bool $predicate
     */
    public function every(callable $predicate): bool
    {
        foreach ($this->elements as $key => $value) {
            if (!$predicate($value, $key)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns a new sorted collection. Defaults to SORT_REGULAR behaviour.
     * Note that internally this method uses asort so index association is maintained.
     *
     * @see https://www.php.net/manual/en/function.asort.php
     *
     * @param int $flags Flags to pass to asort
     *
     * @return Collection<TKey,T>
     */
    public function sort(int $flags = \SORT_REGULAR): self
    {
        $copy = $this->toArray();

        asort($copy, $flags);

        return new self($copy);
    }

    /**
     * Returns a new collection which is sorted with a comparator function.
     * Note that internally this method uses uasort so index association is maintained.
     *
     * @see https://www.php.net/manual/en/function.asort.php
     * @see https://www.php.net/manual/en/function.uasort.php
     *
     * @param callable(T,T):int $comparator
     *
     * @return Collection<TKey,T>
     */
    public function usort(callable $comparator): self
    {
        $copy = $this->toArray();

        uasort($copy, $comparator);

        return new self($copy);
    }

    /**
     * Returns a new collection which is sorted by the collection's keys.
     *
     * @see https://www.php.net/manual/en/function.ksort.php
     *
     * @return Collection<TKey,T>
     */
    public function ksort(int $flags = \SORT_REGULAR): self
    {
        $copy = $this->toArray();

        ksort($copy, $flags);

        return new self($copy);
    }

    /**
     * Returns whether the collection is empty.
     */
    public function isEmpty(): bool
    {
        return empty($this->elements);
    }

    /**
     * Clears the collection.
     *
     * @return Collection<TKey,T>
     */
    public function clear(): self
    {
        $this->elements = [];

        return $this;
    }

    /**
     * Returns whether the collection is a list as per array_is_list.
     */
    public function isList(): bool
    {
        if (\PHP_VERSION_ID >= 80100) {
            return array_is_list($this->elements);
        }

        return $this->isEmpty() || $this->elements === array_values($this->elements);
    }

    /**
     * Returns the number of elements in the collection.
     */
    public function count(): int
    {
        return \count($this->elements);
    }

    /**
     * @template U
     *
     * @return Collection<TKey, U>
     *
     * @psalm-suppress MixedAssignment
     * @psalm-suppress MixedArrayAccess
     */
    public function pluck(string $field): self
    {
        $plucked = [];
        foreach ($this->elements as $key => $value) {
            if (\is_array($value) || $value instanceof \ArrayAccess) {
                $newValue = $value[$field];
            } elseif (\is_object($value)) {
                $newValue = $value->$field;
            } else {
                throw new \LogicException('Collection values must be either arrays or objects');
            }

            $plucked[$key] = $newValue;
        }

        return new self($plucked);
    }

    /**
     * @return Collection<TKey, T>
     */
    public function slice(int $offset, ?int $length, bool $preserveKeys = false): self
    {
        return new self(\array_slice($this->elements, $offset, $length, $preserveKeys));
    }

    /**
     * @param iterable<array-key, T> ...$iterables
     *
     * @return Collection<TKey, T>
     */
    public function diff(iterable ...$iterables): self
    {
        $arrays = [];
        foreach ($iterables as $iterable) {
            $arrays[] = [...$iterable];
        }

        return new self(array_diff($this->elements, ...$arrays));
    }

    /**
     * @param iterable<array-key, T> ...$iterables
     *
     * @return Collection<TKey, T>
     */
    public function intersect(iterable ...$iterables): self
    {
        $arrays = [];
        foreach ($iterables as $iterable) {
            $arrays[] = [...$iterable];
        }

        return new self(array_intersect($this->elements, ...$arrays));
    }

    /**
     * @return Collection<TKey, T>
     */
    public function unique(bool $strict = false): self
    {
        if ($strict) {
            $result = [];
            foreach ($this->elements as $key => $value) {
                if (!\in_array($value, $result, true)) {
                    $result[$key] = $value;
                }
            }

            return new self($result);
        }

        return new self(array_unique($this->elements));
    }

    /**
     * @psalm-suppress MixedArgumentTypeCoercion
     */
    public function implode(string $separator = ','): string
    {
        return implode($separator, array_values($this->elements));
    }

    public function reverse(bool $preserveKeys = false): self
    {
        return new self(array_reverse($this->elements, $preserveKeys));
    }

    public function flip(): self
    {
        /** @psalm-suppress PossiblyInvalidArgument */
        return new self(array_flip($this->elements));
    }

    /**
     * @return Collection<int, T>
     */
    public function flatten(): self
    {
        $result = [];

        array_walk_recursive(
            $this->elements,
            /** @param T $a */
            function (mixed $a) use (&$result) {
                \assert(\is_array($result));
                $result[] = $a;
            }
        );

        /** @var array<int, T> $result */

        return new self($result);
    }

    /**
     * Returns the collection as a native array.
     *
     * @return array<TKey,T>
     */
    public function toArray(): array
    {
        return $this->elements;
    }

    /**
     * @return \Traversable<TKey,T>
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->elements);
    }

    /**
     * @param TKey $offset
     */
    public function offsetExists($offset): bool
    {
        return $this->containsKey($offset);
    }

    /**
     * @param TKey $offset
     *
     * @return T
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    /**
     * @param TKey|null $offset
     * @param T         $value
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (!isset($offset)) {
            $this->add($value);

            return;
        }

        $this->set($offset, $value);
    }

    /**
     * @param TKey $offset
     */
    public function offsetUnset(mixed $offset): void
    {
        $this->remove($offset);
    }

    public function jsonSerialize(): array
    {
        return $this->elements;
    }
}
