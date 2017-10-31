<?php

namespace Palmtree\Collection\Test;

use Palmtree\Collection\Collection;
use PHPUnit\Framework\TestCase;

class CollectionArrayAccessTest extends TestCase
{
    public function testOffsetExists()
    {
        $collection = new Collection();

        $collection->set('foo', 'Bar');

        $this->assertTrue(isset($collection['foo']));
    }

    public function testOffsetGet()
    {
        $collection = new Collection();

        $collection->set('foo', 'Bar');

        $this->assertEquals('Bar', $collection['foo']);
    }

    public function testOffsetSet()
    {
        $collection = new Collection();

        $collection['foo'] = 'Bar';

        $this->assertEquals('Bar', $collection->get('foo'));

        $collection = new Collection();

        $collection[] = 'Bar';

        $this->assertEquals('Bar', $collection[0]);
    }

    public function testOffsetUnset()
    {
        $collection = new Collection();

        $collection->set('foo', 'Bar');

        unset($collection['foo']);

        $this->assertFalse($collection->containsKey('foo'));
    }
}
