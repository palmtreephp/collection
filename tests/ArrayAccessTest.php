<?php

namespace Palmtree\Collection\Test;

use Palmtree\Collection\Exception\BadMethodCallException;
use Palmtree\Collection\Map;
use Palmtree\Collection\Sequence;
use PHPUnit\Framework\TestCase;

class ArrayAccessTest extends TestCase
{
    public function testOffsetExists()
    {
        $map = new Map();

        $map->set('foo', 'Bar');

        $this->assertTrue(isset($map['foo']));
    }

    public function testOffsetGet()
    {
        $map = new Map();

        $map->set('foo', 'Bar');

        $this->assertEquals('Bar', $map['foo']);
    }

    public function testOffsetSet()
    {
        $map = new Map();

        $map['foo'] = 'Bar';

        $this->assertEquals('Bar', $map->get('foo'));

        $sequence = new Sequence();

        $sequence[] = 'Bar';

        $this->assertEquals('Bar', $sequence[0]);
    }

    public function testOffsetSetWithValueOnSequenceThrowsException()
    {
        $this->expectException(BadMethodCallException::class);

        $sequence    = new Sequence();
        $sequence[0] = 'Foo';
    }

    public function testOffsetUnset()
    {
        $collection = new Map();

        $collection->set('foo', 'Bar');

        unset($collection['foo']);

        $this->assertFalse($collection->hasKey('foo'));
    }
}
