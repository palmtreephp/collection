<?php declare(strict_types=1);

namespace Palmtree\Collection\Test;

use Palmtree\Collection\Exception\BadMethodCallException;
use Palmtree\Collection\Map;
use Palmtree\Collection\Sequence;
use PHPUnit\Framework\TestCase;

class ArrayAccessTest extends TestCase
{
    public function testOffsetExists(): void
    {
        $map = new Map();

        $map->set('foo', 'Bar');

        $this->assertTrue(isset($map['foo']));
    }

    public function testOffsetGet(): void
    {
        $map = new Map();

        $map->set('foo', 'Bar');

        $this->assertEquals('Bar', $map['foo']);
    }

    public function testOffsetSet(): void
    {
        $map = new Map();

        $map['foo'] = 'Bar';

        $this->assertEquals('Bar', $map->get('foo'));

        $sequence = new Sequence();

        $sequence[] = 'Bar';

        $this->assertEquals('Bar', $sequence[0]);
    }

    public function testOffsetSetWithValueOnSequenceThrowsException(): void
    {
        $this->expectException(BadMethodCallException::class);

        $sequence    = new Sequence();
        $sequence[0] = 'Foo';
    }

    public function testOffsetUnset(): void
    {
        $collection = new Map();

        $collection->set('foo', 'Bar');

        unset($collection['foo']);

        $this->assertFalse($collection->containsKey('foo'));
    }
}
