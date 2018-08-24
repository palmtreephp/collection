<?php

namespace Palmtree\Collection\Test;

use Palmtree\Collection\Map;
use Palmtree\Collection\Sequence;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    public function testPushArray()
    {
        $collection = new Sequence();

        $collection
            ->push(1)
            ->push(2)
            ->push(3);

        $this->assertSame([1, 2, 3], $collection->toArray());
    }

    public function testAdd()
    {
        $collection = new Sequence();

        $data = [
            'Bar',
            'Bez',
            'Bah',
        ];

        $collection->add($data);

        $this->assertSame($data, $collection->toArray());
    }

    public function testRemove()
    {
        $collection = new Sequence();

        $object = new \stdClass();
        $collection->push($object);

        $this->assertTrue($collection->has($object));

        $collection->removeItem($object);

        $this->assertFalse($collection->has($object));
    }

    public function testHas()
    {
        $collection = new Sequence();

        $collection->add([1, 2, 3]);

        $this->assertTrue($collection->has(2));
    }

    public function testIsEmpty()
    {
        $collection = new Sequence();

        $collection
            ->push('Bar')
            ->push(null);

        $this->assertFalse($collection->isEmpty());

        $collection->clear();

        $this->assertTrue($collection->isEmpty());
    }

    public function testFirstLast()
    {
        $collection = new Sequence();

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
        $collection = new Sequence();
        $collection->push('Foo');

        $collection->clear();

        $this->assertEmpty($collection);
    }

    public function testIterator()
    {
        $collection = new Sequence();

        $collection
            ->push(1)
            ->push(2)
            ->push(3);

        $this->assertSame([1, 2, 3], iterator_to_array($collection));
    }

    public function testSerialization()
    {
        $collection = new Sequence('int');

        $collection
            ->push(1)
            ->push(2)
            ->push(3);

        $serialized = serialize($collection);

        /** @var Map $newCollection */
        $newCollection = unserialize($serialized);

        $this->assertEquals('int', $newCollection->getValidator()->getType());

        $this->assertSame([1, 2, 3], $collection->toArray());
    }

    public function testFilter()
    {
        $collection = new Sequence('int');

        $collection
            ->push(1)
            ->push(2)
            ->push(3);

        $filtered = $collection->filter(function ($item) {
            return $item < 3;
        });

        $this->assertNotSame($collection, $filtered);
        $this->assertFalse($filtered->has(3));
    }

    public function testJsonSerialize()
    {
        $collection = new Sequence('int');

        $collection
            ->push(1)
            ->push(2)
            ->push(3);

        $json = json_encode($collection);

        $this->assertSame('[1,2,3]', $json);

        $collectionFromJson = Sequence::fromJson($json, $collection->getValidator()->getType());

        $this->assertEquals($collection, $collectionFromJson);
    }
}
