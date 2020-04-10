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

        $sequence->removeElement($object);

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

        $filtered = $sequence->filter(function ($element) {
            return $element < 3;
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

    public function testEvery()
    {
        $sequence = Sequence::fromArray([1, 30, 39, 29, 10, 13]);

        $this->assertTrue($sequence->every(function ($value) {
            return $value < 40;
        }));
    }

    public function testReduce()
    {
        $sequence = Sequence::fromArray(['1', '2', '3', '4', '5']);

        $this->assertSame('12345', $sequence->reduce(function ($prev, $cur) {
            return $prev . $cur;
        }, ''));
    }

    public function testReduceRight()
    {
        $sequence = Sequence::fromArray(['1', '2', '3', '4', '5']);

        $this->assertSame('54321', $sequence->reduceRight(function ($prev, $cur) {
            return $prev . $cur;
        }, ''));
    }

    public function testIndex()
    {
        $obj1     = new \stdClass();
        $obj1->id = 'foo';

        $obj2     = new \stdClass();
        $obj2->id = 'bar';

        $obj3     = new \stdClass();
        $obj3->id = 'baz';

        $obj4     = new \stdClass();
        $obj4->id = 'qux';

        $sequence = new Sequence(\stdClass::class);
        $sequence->addIndex('id', function ($obj) {
            return $obj->id;
        });
        $sequence->push($obj1, $obj2, $obj3, $obj4);

        $this->assertSame($obj1, $sequence->getBy('id', 'foo'));
        $this->assertSame($obj2, $sequence->getBy('id', 'bar'));
        $this->assertSame($obj3, $sequence->getBy('id', 'baz'));
        $this->assertSame($obj4, $sequence->getBy('id', 'qux'));

        $this->assertSame($obj4, $sequence->pop());

        $this->assertNull($sequence->getBy('id', 'qux'));
        $this->assertSame($obj1, $sequence->getBy('id', 'foo'));
        $this->assertSame($obj2, $sequence->getBy('id', 'bar'));
        $this->assertSame($obj3, $sequence->getBy('id', 'baz'));
    }

    public function testIndexAfterShift()
    {
        $obj1     = new \stdClass();
        $obj1->id = 'foo';

        $obj2     = new \stdClass();
        $obj2->id = 'bar';

        $obj3     = new \stdClass();
        $obj3->id = 'baz';

        $sequence = new Sequence(\stdClass::class);
        $sequence->addIndex('id', function ($obj) {
            return $obj->id;
        });

        $sequence->push($obj1, $obj2, $obj3);

        $this->assertSame($obj1, $sequence->shift());

        $this->assertNull($sequence->getBy('id', 'foo'));
        $this->assertSame($obj2, $sequence->getBy('id', 'bar'));
        $this->assertSame($obj3, $sequence->getBy('id', 'baz'));
    }

    public function testIndexAfterUnShift()
    {
        $obj1     = new \stdClass();
        $obj1->id = 'foo';

        $obj2     = new \stdClass();
        $obj2->id = 'bar';

        $obj3     = new \stdClass();
        $obj3->id = 'baz';

        $obj4     = new \stdClass();
        $obj4->id = 'qux';

        $sequence = new Sequence(\stdClass::class);
        $sequence->addIndex('id', function ($obj) {
            return $obj->id;
        });

        $sequence->push($obj1, $obj2);

        $this->assertSame($obj1, $sequence->getBy('id', 'foo'));
        $this->assertSame($obj2, $sequence->getBy('id', 'bar'));

        $sequence->unshift($obj3, $obj4);

        $this->assertSame($obj1, $sequence->getBy('id', 'foo'));
        $this->assertSame($obj2, $sequence->getBy('id', 'bar'));
        $this->assertSame($obj3, $sequence->getBy('id', 'baz'));
        $this->assertSame($obj4, $sequence->getBy('id', 'qux'));
    }

    public function testSort()
    {
        $sequence = Sequence::fromArray(['4', '5', '3', '1', '2']);

        $sequence->sort();

        $this->assertEquals(['1', '2', '3', '4', '5'], $sequence->toArray());
    }

    public function testSortWithComparator()
    {
        $sequence = Sequence::fromArray(['4', '5', '3', '1', '2']);

        $sequence->sort(function ($a, $b) {
            return $b <=> $a;
        });

        $this->assertEquals(['5', '4', '3', '2', '1'], $sequence->toArray());
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
