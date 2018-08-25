<?php

namespace Palmtree\Collection\Test;

use Palmtree\Collection\Map;
use Palmtree\Collection\Sequence;
use PHPUnit\Framework\TestCase;

class SequenceTest extends TestCase
{
    public function testPush()
    {
        $collection = new Sequence();

        $collection->push(1, 2, 3);

        $this->assertSame([1, 2, 3], $collection->toArray());
    }

    public function testPop()
    {
        $sequence = new Sequence();

        $sequence->push(1, 2, 3);

        $this->assertSame(3, $sequence->pop());
        $this->assertFalse($sequence->has(3));
    }

    public function testShift()
    {
        $sequence = new Sequence();

        $sequence->push(1, 2, 3);

        $this->assertSame(1, $sequence->shift());
        $this->assertFalse($sequence->has(1));
    }

    public function testUnshift()
    {
        $sequence = new Sequence();

        $sequence->push(2, 3);

        $sequence->unshift(1);

        $this->assertSame(1, $sequence->first());
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

        $collection->push($objectOne, $objectTwo, $objectThree);

        $this->assertSame($objectOne, $collection->first());
        $this->assertNotSame($objectTwo, $collection->first());

        $this->assertSame($objectThree, $collection->last());
        $this->assertNotSame($objectTwo, $collection->last());
    }

    public function testAll()
    {
        $sequence = new Sequence();

        $sequence->push(1, 2, 3);

        $this->assertSame([1, 2, 3], $sequence->all());
    }

    public function testClear()
    {
        $collection = new Sequence();
        $collection->push('Foo');

        $collection->clear();

        $this->assertEmpty($collection);
    }

    public function testFilter()
    {
        $sequence = new Sequence('int');

        $sequence->push(1, 2, 3);

        $filtered = $sequence->filter(function ($item) {
            return $item < 3;
        });

        $this->assertNotSame($sequence, $filtered);
        $this->assertFalse($filtered->has(3));
    }

    public function testMap()
    {
        $objectOne      = new \stdClass();
        $objectOne->foo = 'Bar';

        $objectTwo      = new \stdClass();
        $objectTwo->foo = 'Baz';

        $sequence = new Sequence(\stdClass::class);
        $sequence->push($objectOne, $objectTwo);

        $mapped = $sequence->map(function (\stdClass $object) {
            return $object->foo;
        }, 'string');

        $this->assertSame(['Bar', 'Baz'], $mapped->toArray());
    }

    public function testIterator()
    {
        $collection = new Sequence();

        $collection->push(1, 2, 3);

        $this->assertSame([1, 2, 3], iterator_to_array($collection));
    }

    public function testSerialization()
    {
        $collection = new Sequence('int');

        $collection->push(1, 2, 3);

        $serialized = serialize($collection);

        /** @var Map $newCollection */
        $newCollection = unserialize($serialized);

        $this->assertEquals('int', $newCollection->getValidator()->getType());

        $this->assertSame([1, 2, 3], $collection->toArray());
    }

    public function testJsonSerialize()
    {
        $collection = new Sequence('int');

        $collection->push(1, 2, 3);

        $json = json_encode($collection);

        $this->assertSame('[1,2,3]', $json);

        $collectionFromJson = Sequence::fromJson($json, $collection->getValidator()->getType());

        $this->assertEquals($collection, $collectionFromJson);
    }
}
