<?php

namespace Palmtree\EasyCollection;

/**
 * @template TKey of array-key
 * @template T
 * @param iterable<TKey, T> $elements
 * @return Collection<TKey, T>
 */
function c(iterable $elements = []): Collection
{
    return new Collection($elements);
}
