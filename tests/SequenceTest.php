<?php

namespace Palmtree\Collection\Test;

use Palmtree\Collection\Map;
use Palmtree\Collection\Sequence;
use PHPUnit\Framework\TestCase;

class SequenceTest extends TestCase
{
    public function testPush()
    {
        $sequence = new Sequence();

        $sequence->push(1, 2, 3);

        $this->assertSame([1, 2, 3], $sequence->toArray());
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
        $sequence = new Sequence();

        $object = new \stdClass();
        $sequence->push($object);

        $this->assertTrue($sequence->has($object));

        $sequence->removeItem($object);

        $this->assertFalse($sequence->has($object));
    }

    public function testHas()
    {
        $sequence = new Sequence();

        $sequence->add([1, 2, 3]);

        $this->assertTrue($sequence->has(2));
    }

    public function testIsEmpty()
    {
        $sequence = new Sequence();

        $sequence
            ->push('Bar')
            ->push(null);

        $this->assertFalse($sequence->isEmpty());

        $sequence->clear();

        $this->assertTrue($sequence->isEmpty());
    }

    public function testFirstLast()
    {
        $sequence = new Sequence();

        $objectOne   = new \stdClass();
        $objectTwo   = new \stdClass();
        $objectThree = new \stdClass();

        $sequence->push($objectOne, $objectTwo, $objectThree);

        $this->assertSame($objectOne, $sequence->first());
        $this->assertNotSame($objectTwo, $sequence->first());

        $this->assertSame($objectThree, $sequence->last());
        $this->assertNotSame($objectTwo, $sequence->last());
    }

    public function testAll()
    {
        $sequence = new Sequence();

        $sequence->push(1, 2, 3);

        $this->assertSame([1, 2, 3], $sequence->all());
    }

    public function testClear()
    {
        $sequence = new Sequence();
        $sequence->push('Foo');

        $sequence->clear();

        $this->assertEmpty($sequence);
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
        $sequence = new Sequence();

        $sequence->push(1, 2, 3);

        $this->assertSame([1, 2, 3], iterator_to_array($sequence));
    }

    public function testSerialization()
    {
        $sequence = new Sequence('int');

        $sequence->push(1, 2, 3);

        $serialized = serialize($sequence);

        /** @var Map $newCollection */
        $newCollection = unserialize($serialized);

        $this->assertEquals('int', $newCollection->getValidator()->getType());

        $this->assertSame([1, 2, 3], $sequence->toArray());
    }

    public function testJsonSerialize()
    {
        $sequence = new Sequence('int');

        $sequence->push(1, 2, 3);

        $json = json_encode($sequence);

        $this->assertSame('[1,2,3]', $json);

        $sequenceFromJson = Sequence::fromJson($json, $sequence->getValidator()->getType());

        $this->assertEquals($sequence, $sequenceFromJson);
    }
}
