<?php

namespace Palmtree\Collection\Test;

use Palmtree\Collection\Map;
use PHPUnit\Framework\TestCase;

class CollectionArrayAccessTest extends TestCase
{
    public function testOffsetExists()
    {
        $collection = new Map();

        $collection->set('foo', 'Bar');

        $this->assertTrue(isset($collection['foo']));
    }

    public function testOffsetGet()
    {
        $collection = new Map();

        $collection->set('foo', 'Bar');

        $this->assertEquals('Bar', $collection['foo']);
    }

    public function testOffsetSet()
    {
        $collection = new Map();

        $collection['foo'] = 'Bar';

        $this->assertEquals('Bar', $collection->get('foo'));

        $collection = new Map();

        $collection[] = 'Bar';

        $this->assertEquals('Bar', $collection[0]);
    }

    public function testOffsetUnset()
    {
        $collection = new Map();

        $collection->set('foo', 'Bar');

        unset($collection['foo']);

        $this->assertFalse($collection->hasKey('foo'));
    }
}
