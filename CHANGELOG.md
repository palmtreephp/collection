# Changelog

All notable changes to this project are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [7.0.0] - 2026-11-17

### Changed

- **BC break:** PHP 8.5 or later is required.
- **BC break:** `get()` and `$collection['key']` throw `OutOfBoundsException` when the key does not exist.
- **BC break:** `remove()` throws `OutOfBoundsException` when the key does not exist. `unset($collection['key'])` on a missing key is still a no-op.
- **BC break:** `pluck()` throws `OutOfBoundsException` when an element does not have the field.
- **BC break:** `implode()` throws `LogicException` when an element is not a scalar, `null` or `Stringable`.
- **BC break:** `flip()` throws `LogicException` when an element is not an int or string.
- **BC break:** `diff()` and `intersect()` compare elements strictly (`===`), so `'1'` and `1` are no longer equal.
- **BC break:** `unique()` compares elements loosely (`==`) by default instead of as strings, and strictly (`===`) when `$strict` is `true`.
- **BC break:** methods that return a new collection, such as `filter()`, `map()` and `sort()`, are marked `#[\NoDiscard]`, so PHP warns when their result is not used.
- Methods that return a new collection return an instance of the called class, so subclasses keep their type.

### Added

- `findKey()` returns the key of the first element that passes a predicate.
- `reduce()` passes each element's key to the callback as a third argument.
- `flatten()` flattens nested collections as well as nested arrays.
- `slice()`'s `$length` argument defaults to `null`.

### Fixed

- `diff()`, `intersect()` and `unique()` work with object and array elements.
- `isset()` and `unset()` with an offset that is not an int or string no longer throw `TypeError`.

## [6.2.1] - 2025-10-30

### Added

- `toList()` returns the elements as a list.

## [6.2.0] - 2025-10-14

### Added

- `pluck()` accepts int fields as well as string fields.

### Changed

- The PHP requirement is `>=8.1` instead of `^8.1`.

### Fixed

- Generic types for `offsetGet()`, `offsetSet()`, `reverse()` and `flip()`.

## [6.1.1] - 2023-11-15

### Changed

- Code formatting.

## [6.1.0] - 2023-11-15

### Changed

- **BC break:** methods that take a callback require a `Closure` instead of any `callable`.

## [6.0.1] - 2023-08-13

### Fixed

- Generic return types of `keys()` and `values()`.

## [6.0.0] - 2023-08-13

### Changed

- **BC break:** the library is rewritten around a single `Collection` class, taken from `palmtree/easy-collection`. It replaces the `Map`, `Sequence` and type validation classes.
- **BC break:** PHP 8.1 or later is required.

[Unreleased]: https://github.com/palmtreephp/collection/compare/v7.0.0...HEAD
[7.0.0]: https://github.com/palmtreephp/collection/compare/v6.2.1...v7.0.0
[6.2.1]: https://github.com/palmtreephp/collection/compare/v6.2.0...v6.2.1
[6.2.0]: https://github.com/palmtreephp/collection/compare/v6.1.1...v6.2.0
[6.1.1]: https://github.com/palmtreephp/collection/compare/v6.1.0...v6.1.1
[6.1.0]: https://github.com/palmtreephp/collection/compare/v6.0.1...v6.1.0
[6.0.1]: https://github.com/palmtreephp/collection/compare/v6.0.0...v6.0.1
[6.0.0]: https://github.com/palmtreephp/collection/releases/tag/v6.0.0
