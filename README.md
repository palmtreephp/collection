# Palmtree Collection

Collection class with optional type validation.

## Requirements
* PHP >= 5.6

## Installation

Use composer to add the package to your dependencies:
```bash
composer require palmtree/collection
```

## Usage

#### Instances of the same class

```php
<?php
use Palmtree\Collection\Collection;

require_once __DIR__ . '/../vendor/autoload.php';

$objects = new Collection();

$objects->setType(\stdClass::class);

$item       = new \stdClass();
$item->data = 'Hello, World!';

$objects->set('key1', $item);
```

#### Single type

Can be anything returned by PHP's [gettype()](http://php.net/manual/en/function.gettype.php#refsect1-function.gettype-returnvalues) function or `float`, `int`, and `bool` for convenience.
```php
<?php
// Arrays
$arrays = new Collection();
$arrays->setType('array');

$items = [];

$items['key1'] = [true, 'Some value', 7];
$items['key2'] = [false, 'Another value', 8];

$arrays->add($items);
```

## License

Released under the [MIT license](LICENSE)
