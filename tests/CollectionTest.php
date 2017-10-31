<?php

namespace Palmtree\Collection\Test;

use Palmtree\Collection\Collection;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    public function testSetGet()
    {
        $collection = new Collection();

        $collection->set('foo', 'Bar');

        $this->assertSame('Bar', $collection->get('foo'));
    }

    public function testPushArray()
    {
        $collection = new Collection();

        $collection
            ->push(1)
            ->push(2)
            ->push(3);

        $this->assertSame([1, 2, 3], $collection->toArray());
    }

    public function testAdd()
    {
        $collection = new Collection();

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
        $collection = new Collection();

        $object = new \stdClass();
        $collection->push($object);

        $this->assertTrue($collection->contains($object));

        $collection->removeItem($object);

        $this->assertFalse($collection->contains($object));
    }

    public function testContains()
    {
        $collection = new Collection();

        $collection->add([1, 2, 3]);

        $this->assertTrue($collection->contains(2));
    }

    public function testContainsKey()
    {
        $collection = new Collection();

        $collection
            ->set('foo', 'Bar')
            ->set('baz', null);

        $this->assertTrue($collection->containsKey('foo'));
        $this->assertTrue($collection->containsKey('baz'));
    }

    public function testFirstLast()
    {
        $collection = new Collection();

        $objectOne   = new \stdClass();
        $objectTwo   = new \stdClass();
        $objectThree = new \stdClass();

        $collection
            ->push($objectOne)
            ->push($objectTwo)
            ->push($objectThree);

        $this->assertSame($objectOne, $collection->first());
        $this->assertNotSame($objectTwo, $collection->first());

        $this->assertSame($objectThree, $collection->last());
        $this->assertNotSame($objectTwo, $collection->last());
    }

    public function testClear()
    {
        $collection = new Collection();
        $collection->push('Foo');

        $collection->clear();

        $this->assertEmpty($collection, 'Derp');
    }

    public function testKeys()
    {
        $collection = new Collection();

        $collection
            ->set('foo', 'Bar')
            ->set('baz', null);

        $this->assertSame(['foo', 'baz'], $collection->getKeys());
    }

    public function testValues()
    {
        $collection = new Collection();

        $objectOne = new \stdClass();
        $objectTwo = new \stdClass();

        $collection
            ->set('foo', $objectOne)
            ->set('baz', $objectTwo);

        $this->assertSame([$objectOne, $objectTwo], $collection->getValues());
    }

    public function testIterator()
    {
        $collection = new Collection();

        $collection
            ->push(1)
            ->push(2)
            ->push(3);

        $this->assertSame([1, 2, 3], iterator_to_array($collection));
    }

    public function testSerialization()
    {
        $collection = new Collection('int');

        $collection
            ->set('foo', 1)
            ->set('bar', 2)
            ->set('baz', 3);

        $serialized = serialize($collection);

        /** @var Collection $newCollection */
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
        $collection = new Collection('int');

        $collection
            ->set('foo', 1)
            ->set('bar', 2)
            ->set('baz', 3);

        $filtered = $collection->filter(function ($item) {
            return $item > 1;
        });

        $this->assertNotSame($collection, $filtered);
        $this->assertFalse($filtered->containsKey('foo'));
    }
}
