<?php

declare(strict_types=1);

namespace Palmtree\Collection;

/**
 * @template TKey of array-key
 * @template T
 *
 * @template-implements \IteratorAggregate<TKey,T>
 * @template-implements \ArrayAccess<TKey,T>
 *
 * @phpstan-consistent-constructor
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
     *
     * @throws \OutOfBoundsException if the key does not exist
     */
    public function get(int|string $key): mixed
    {
        if (!$this->containsKey($key)) {
            throw new \OutOfBoundsException(\sprintf('Key "%s" does not exist in the collection', $key));
        }

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
            // Appending assigns an int key. TKey can't be constrained per method, so this is
            // only type-safe for int-keyed collections.
            $this->elements[] = $el; // @phpstan-ignore assign.propertyType
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
        return new static(array_keys($this->elements));
    }

    /**
     * Returns a new collection containing the current collection's values.
     *
     * @return Collection<int, T>
     */
    public function values(): self
    {
        return new static(array_values($this->elements));
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
        return array_first($this->elements);
    }

    /**
     * Returns the last element in the collection.
     *
     * @return T|null
     */
    public function last(): mixed
    {
        return array_last($this->elements);
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
     * @param \Closure(T, TKey, int): (bool|void) $callback
     *
     * @return $this
     */
    public function each(\Closure $callback): self
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
     * @param \Closure(T, TKey): bool $predicate
     *
     * @return T|null
     */
    public function find(\Closure $predicate): mixed
    {
        return array_find($this->elements, $predicate);
    }

    /**
     * Returns a new collection containing all elements which pass the predicate function.
     *
     * @param ?\Closure(T, TKey): bool $predicate
     *
     * @return Collection<TKey,T>
     */
    public function filter(?\Closure $predicate = null): self
    {
        $result = array_filter($this->elements, $predicate, \ARRAY_FILTER_USE_BOTH);

        return new static($result);
    }

    /**
     * Returns a new collection whose values are mapped by the callback function.
     *
     * @param \Closure(T, TKey): U $callback
     *
     * @return Collection<TKey, U>
     *
     * @template U
     */
    public function map(\Closure $callback): self
    {
        $mapped = [];

        foreach ($this->elements as $key => $element) {
            $mapped[$key] = $callback($element, $key);
        }

        return new static($mapped);
    }

    /**
     * Reduces the collection a single value.
     *
     * @param \Closure(mixed, T): mixed $callback
     */
    public function reduce(\Closure $callback, mixed $initial = null): mixed
    {
        return array_reduce($this->elements, $callback, $initial);
    }

    /**
     * Returns whether at least one element passes the predicate function.
     *
     * @param \Closure(T, TKey): bool $predicate
     */
    public function some(\Closure $predicate): bool
    {
        return array_any($this->elements, $predicate);
    }

    /**
     * Returns whether all elements pass the predicate function.
     *
     * @param \Closure(T, TKey): bool $predicate
     */
    public function every(\Closure $predicate): bool
    {
        return array_all($this->elements, $predicate);
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

        return new static($copy);
    }

    /**
     * Returns a new collection which is sorted with a comparator function.
     * Note that internally this method uses uasort so index association is maintained.
     *
     * @see https://www.php.net/manual/en/function.asort.php
     * @see https://www.php.net/manual/en/function.uasort.php
     *
     * @param \Closure(T,T): int $comparator
     *
     * @return Collection<TKey,T>
     */
    public function usort(\Closure $comparator): self
    {
        $copy = $this->toArray();

        uasort($copy, $comparator);

        return new static($copy);
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

        return new static($copy);
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
        return array_is_list($this->elements);
    }

    /**
     * Returns the number of elements in the collection.
     */
    public function count(): int
    {
        return \count($this->elements);
    }

    /**
     * @return Collection<TKey, mixed>
     */
    public function pluck(string|int $field): self
    {
        $plucked = [];

        foreach ($this->elements as $key => $value) {
            if (\is_array($value) || $value instanceof \ArrayAccess) {
                /** @var \ArrayAccess<array-key, mixed>|array<array-key, mixed> $value */
                $newValue = $value[$field];
            } elseif (\is_object($value)) {
                $newValue = $value->$field;
            } else {
                throw new \LogicException('Collection values must be either arrays or objects when using pluck()');
            }

            $plucked[$key] = $newValue;
        }

        return new static($plucked);
    }

    /**
     * @return Collection<TKey, T>
     */
    public function slice(int $offset, ?int $length = null, bool $preserveKeys = false): self
    {
        return new static(\array_slice($this->elements, $offset, $length, $preserveKeys));
    }

    /**
     * Returns a new collection containing the elements not present in any of the given iterables.
     * Elements are compared strictly (===).
     *
     * @param iterable<array-key, T> ...$iterables
     *
     * @return Collection<TKey, T>
     */
    public function diff(iterable ...$iterables): self
    {
        $others = self::mergeValues($iterables);

        return $this->filter(static fn (mixed $element): bool => !\in_array($element, $others, true));
    }

    /**
     * Returns a new collection containing the elements present in every one of the given iterables.
     * Elements are compared strictly (===).
     *
     * @param iterable<array-key, T> ...$iterables
     *
     * @return Collection<TKey, T>
     */
    public function intersect(iterable ...$iterables): self
    {
        $others = array_map(static fn (iterable $iterable): array => self::mergeValues([$iterable]), $iterables);

        return $this->filter(static function (mixed $element) use ($others): bool {
            foreach ($others as $values) {
                if (!\in_array($element, $values, true)) {
                    return false;
                }
            }

            return true;
        });
    }

    /**
     * Returns a new collection with duplicate elements removed, keeping the first occurrence of each.
     * Elements are compared loosely (==) by default, or strictly (===) when $strict is true.
     *
     * @return Collection<TKey, T>
     */
    public function unique(bool $strict = false): self
    {
        $result = [];
        foreach ($this->elements as $key => $value) {
            if (!\in_array($value, $result, $strict)) {
                $result[$key] = $value;
            }
        }

        return new static($result);
    }

    public function implode(string $separator = ','): string
    {
        return implode($separator, array_values($this->elements));
    }

    /**
     * @return self<TKey, T>
     */
    public function reverse(bool $preserveKeys = false): self
    {
        return new static(array_reverse($this->elements, $preserveKeys));
    }

    /**
     * @return self<array-key, TKey>
     */
    public function flip(): self
    {
        return new static(array_flip($this->elements));
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
            static function (mixed $a) use (&$result): void {
                $result[] = $a;
            },
        );

        /* @var array<int, T> $result */

        return new static($result);
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
     * Returns the collection as a native array with sequential numeric keys.
     *
     * @return list<T>
     */
    public function toList(): array
    {
        return array_values($this->elements);
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->elements);
    }

    public function offsetExists(mixed $offset): bool
    {
        return self::isKey($offset) && $this->containsKey($offset);
    }

    /**
     * @return T
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    /**
     * @param T $value
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (!isset($offset)) {
            $this->add($value);

            return;
        }

        /** @var TKey $offset */
        $this->set($offset, $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        if (self::isKey($offset)) {
            $this->remove($offset);
        }
    }

    /**
     * PHP passes offsets of any type to the ArrayAccess methods, e.g. isset($collection[true]).
     */
    private static function isKey(mixed $offset): bool
    {
        return \is_int($offset) || \is_string($offset);
    }

    /**
     * @param array<iterable<array-key, T>> $iterables
     *
     * @return list<T>
     */
    private static function mergeValues(array $iterables): array
    {
        $values = [];
        foreach ($iterables as $iterable) {
            foreach ($iterable as $value) {
                $values[] = $value;
            }
        }

        return $values;
    }

    /**
     * @return array<TKey,T>
     */
    public function jsonSerialize(): array
    {
        return $this->elements;
    }
}
