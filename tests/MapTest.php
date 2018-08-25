<?php

namespace Palmtree\Collection\Test;

use Palmtree\Collection\Map;
use PHPUnit\Framework\TestCase;

class MapTest extends TestCase
{
    public function testSetGet()
    {
        $collection = new Map();

        $collection->set('foo', 'Bar');

        $this->assertSame('Bar', $collection->get('foo'));
    }

    public function testAdd()
    {
        $collection = new Map();

        $data = [
            'foo'  => 'Bar',
            'baz'  => 'Bez',
            'bing' => 'Bah',
        ];

        $collection->add($data);

        $this->assertSame($data, $collection->toArray());
    }

    public function testRemove()
    {
        $collection = new Map();

        $object = new \stdClass();
        $collection->set('foo', $object);

        $this->assertTrue($collection->has($object));

        $collection->removeItem($object);

        $this->assertFalse($collection->has($object));
    }

    public function testHas()
    {
        $collection = new Map();

        $collection->add([1, 2, 3]);

        $this->assertTrue($collection->has(2));
    }

    public function testHasKey()
    {
        $collection = new Map();

        $collection
            ->set('foo', 'Bar')
            ->set('baz', null);

        $this->assertTrue($collection->hasKey('foo'));
        $this->assertTrue($collection->hasKey('baz'));
    }

    public function testIsEmpty()
    {
        $collection = new Map();

        $collection
            ->set('foo', 'Bar')
            ->set('baz', null);

        $this->assertFalse($collection->isEmpty());

        $collection->clear();

        $this->assertTrue($collection->isEmpty());
    }

    public function testFirstLast()
    {
        $collection = new Map();

        $objectOne   = new \stdClass();
        $objectTwo   = new \stdClass();
        $objectThree = new \stdClass();

        $collection
            ->set('one', $objectOne)
            ->set('two', $objectTwo)
            ->set('three', $objectThree);

        $this->assertSame($objectOne, $collection->first());
        $this->assertNotSame($objectTwo, $collection->first());

        $this->assertSame($objectThree, $collection->last());
        $this->assertNotSame($objectTwo, $collection->last());
    }

    public function testClear()
    {
        $collection = new Map();
        $collection->set('foo', 'Foo');

        $collection->clear();

        $this->assertEmpty($collection);
    }

    public function testKeys()
    {
        $collection = new Map();

        $collection
            ->set('foo', 'Bar')
            ->set('baz', null);

        $this->assertSame(['foo', 'baz'], $collection->getKeys()->toArray());
    }

    public function testValues()
    {
        $collection = new Map();

        $objectOne = new \stdClass();
        $objectTwo = new \stdClass();

        $collection
            ->set('foo', $objectOne)
            ->set('baz', $objectTwo);

        $this->assertSame([$objectOne, $objectTwo], $collection->getValues()->toArray());
    }

    public function testIterator()
    {
        $collection = new Map();

        $collection
            ->set('one', 1)
            ->set('two', 2)
            ->set('three', 3);

        $expected = [
            'one'   => 1,
            'two'   => 2,
            'three' => 3,
        ];

        $this->assertSame($expected, iterator_to_array($collection));
    }

    public function testSerialization()
    {
        $collection = new Map('int');

        $collection
            ->set('foo', 1)
            ->set('bar', 2)
            ->set('baz', 3);

        $serialized = serialize($collection);

        /** @var Map $newCollection */
        $newCollection = unserialize($serialized);

        $this->assertEquals('int', $newCollection->getValidator()->getType());

        $expected = [
            'foo' => 1,
            'bar' => 2,
            'baz' => 3,
        ];

        $this->assertSame($expected, $collection->toArray());
    }

    public function testFilter()
    {
        $map = new Map('int');

        $map
            ->set('foo', 1)
            ->set('bar', 2)
            ->set('baz', 3);

        $filtered = $map->filter(function ($element) {
            return $element > 1;
        });

        $this->assertNotSame($map, $filtered);
        $this->assertFalse($filtered->hasKey('foo'));

        $map = new Map('bool');

        $map
            ->set('foo', true)
            ->set('bar', false)
            ->set('baz', true);

        $filtered = $map->filter();

        $this->assertNotSame($map, $filtered);
        $this->assertFalse($filtered->hasKey('bar'));
    }

    public function testFilterWithKeys()
    {
        $collection = new Map('int');

        $collection
            ->set('foo', 1)
            ->set('bar', 2)
            ->set('baz', 3);

        $filtered = $collection->filter(function ($element, $key) {
            return $key !== 'foo';
        }, true);

        $this->assertNotSame($collection, $filtered);
        $this->assertFalse($filtered->hasKey('foo'));
    }

    public function testMapWithKeys()
    {
        $map = new Map();

        $map
            ->set('foo', 1)
            ->set('bar', 2)
            ->set('baz', 3);

        $mapped = $map->map(function ($element, $key) {
            if ($key === 'bar') {
                return 4;
            }

            return $element;
        }, null, true);

        $this->assertSame(4, $mapped['bar']);
    }

    public function testJsonSerialize()
    {
        $collection = new Map('int');

        $collection
            ->set('foo', 1)
            ->set('bar', 2)
            ->set('baz', 3);

        $json = json_encode($collection);

        $this->assertSame('{"foo":1,"bar":2,"baz":3}', $json);

        $collectionFromJson = Map::fromJson($json, $collection->getValidator()->getType());

        $this->assertEquals($collection, $collectionFromJson);
    }
}
