<?php

namespace Palmtree\Collection\Test;

use Palmtree\Collection\Exception\OutOfBoundsException;
use Palmtree\Collection\Map;
use Palmtree\Collection\Sequence;
use PHPUnit\Framework\TestCase;

class SequenceTest extends TestCase
{
    public function testPush(): void
    {
        $sequence = new Sequence();

        $sequence->push(1, 2, 3);

        $this->assertSame([1, 2, 3], $sequence->toArray());
    }

    public function testPop(): void
    {
        $sequence = new Sequence();

        $sequence->push(1, 2, 3);

        $this->assertSame(3, $sequence->pop());
        $this->assertFalse($sequence->contains(3));
    }

    public function testShift(): void
    {
        $sequence = new Sequence();

        $sequence->push(1, 2, 3);

        $this->assertSame(1, $sequence->shift());
        $this->assertFalse($sequence->contains(1));
    }

    public function testUnshift(): void
    {
        $sequence = new Sequence();

        $sequence->push(2, 3);

        $sequence->unshift(1);

        $this->assertSame(1, $sequence->first());
    }

    public function testRemove(): void
    {
        $sequence = new Sequence();

        $object = new \stdClass();
        $sequence->push($object);

        $this->assertTrue($sequence->contains($object));

        $sequence->removeElement($object);

        $this->assertFalse($sequence->contains($object));
    }

    public function testHas(): void
    {
        $sequence = new Sequence();

        $sequence->add([1, 2, 3]);

        $this->assertTrue($sequence->contains(2));
    }

    public function testIsEmpty(): void
    {
        $sequence = new Sequence();

        $sequence
            ->push('Bar')
            ->push(null);

        $this->assertFalse($sequence->isEmpty());

        $sequence->clear();

        $this->assertTrue($sequence->isEmpty());
    }

    public function testFirstLast(): void
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

    public function testAll(): void
    {
        $sequence = new Sequence();

        $sequence->push(1, 2, 3);

        $this->assertSame([1, 2, 3], $sequence->all());
    }

    /**
     * @psalm-suppress TypeDoesNotContainType
     */
    public function testClear(): void
    {
        $sequence = new Sequence();
        $sequence->push('Foo');

        $sequence->clear();

        $this->assertEmpty($sequence);
    }

    public function testFilter(): void
    {
        $sequence = new Sequence('int');

        $sequence->push(1, 2, 3);

        $filtered = $sequence->filter(fn ($element) => $element < 3);

        $this->assertNotSame($sequence, $filtered);
        $this->assertFalse($filtered->contains(3));
    }

    public function testMap(): void
    {
        $objectOne      = new \stdClass();
        $objectOne->foo = 'Bar';

        $objectTwo      = new \stdClass();
        $objectTwo->foo = 'Baz';

        $sequence = new Sequence(\stdClass::class);
        $sequence->push($objectOne, $objectTwo);

        $mapped = $sequence->map(fn (\stdClass $object) => $object->foo, 'string');

        $this->assertSame(['Bar', 'Baz'], $mapped->toArray());
    }

    public function testEvery(): void
    {
        $sequence = Sequence::fromArray([1, 30, 39, 29, 10, 13]);

        $this->assertTrue($sequence->every(fn ($value) => $value < 40));
    }

    public function testReduce(): void
    {
        $sequence = Sequence::fromArray(['1', '2', '3', '4', '5']);

        $this->assertSame('12345', $sequence->reduce(fn ($prev, $cur) => $prev . $cur, ''));
    }

    public function testReduceRight(): void
    {
        $sequence = Sequence::fromArray(['1', '2', '3', '4', '5']);

        $this->assertSame('54321', $sequence->reduceRight(fn ($prev, $cur) => $prev . $cur, ''));
    }

    public function testIndex(): void
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
        $sequence->addIndex('id', fn ($obj) => $obj->id);
        $sequence->push($obj1, $obj2, $obj3, $obj4);

        $this->assertSame($obj1, $sequence->getBy('id', 'foo'));
        $this->assertSame($obj2, $sequence->getBy('id', 'bar'));
        $this->assertSame($obj3, $sequence->getBy('id', 'baz'));
        $this->assertSame($obj4, $sequence->getBy('id', 'qux'));

        $this->assertSame($obj4, $sequence->pop());

        $this->assertSame($obj1, $sequence->getBy('id', 'foo'));
        $this->assertSame($obj2, $sequence->getBy('id', 'bar'));
        $this->assertSame($obj3, $sequence->getBy('id', 'baz'));

        $this->expectException(OutOfBoundsException::class);
        $sequence->getBy('id', 'qux');
    }

    public function testIndexAfterShift(): void
    {
        $obj1     = new \stdClass();
        $obj1->id = 'foo';

        $obj2     = new \stdClass();
        $obj2->id = 'bar';

        $obj3     = new \stdClass();
        $obj3->id = 'baz';

        $sequence = new Sequence(\stdClass::class);
        $sequence->addIndex('id', fn ($obj) => $obj->id);

        $sequence->push($obj1, $obj2, $obj3);

        $this->assertSame($obj1, $sequence->shift());

        $this->assertSame($obj2, $sequence->getBy('id', 'bar'));
        $this->assertSame($obj3, $sequence->getBy('id', 'baz'));

        $this->expectException(OutOfBoundsException::class);
        $sequence->getBy('id', 'foo');
    }

    public function testIndexAfterUnShift(): void
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
        $sequence->addIndex('id', fn ($obj) => $obj->id);

        $sequence->push($obj1, $obj2);

        $this->assertSame($obj1, $sequence->getBy('id', 'foo'));
        $this->assertSame($obj2, $sequence->getBy('id', 'bar'));

        $sequence->unshift($obj3, $obj4);

        $this->assertSame($obj1, $sequence->getBy('id', 'foo'));
        $this->assertSame($obj2, $sequence->getBy('id', 'bar'));
        $this->assertSame($obj3, $sequence->getBy('id', 'baz'));
        $this->assertSame($obj4, $sequence->getBy('id', 'qux'));
    }

    public function testSort(): void
    {
        $sequence = Sequence::fromArray(['4', '5', '3', '1', '2']);

        $sequence->sort();

        $this->assertEquals(['1', '2', '3', '4', '5'], $sequence->toArray());
    }

    public function testSortWithComparator(): void
    {
        $sequence = Sequence::fromArray(['4', '5', '3', '1', '2']);

        $sequence->sort(fn ($a, $b) => $b <=> $a);

        $this->assertEquals(['5', '4', '3', '2', '1'], $sequence->toArray());
    }

    public function testSorted(): void
    {
        $sequence = Sequence::fromArray(['4', '5', '3', '1', '2']);

        $sortedSequence = $sequence->sorted();

        $this->assertNotSame($sequence, $sortedSequence);
        $this->assertEquals(['1', '2', '3', '4', '5'], $sortedSequence->toArray());
        $this->assertEquals(['4', '5', '3', '1', '2'], $sequence->toArray());
    }

    public function testIterator(): void
    {
        $sequence = new Sequence();

        $sequence->push(1, 2, 3);

        $this->assertSame([1, 2, 3], iterator_to_array($sequence));
    }

    public function testSerialization(): void
    {
        $sequence = new Sequence('int');

        $sequence->push(1, 2, 3);

        $serialized = serialize($sequence);

        /** @var Map $newCollection */
        $newCollection = unserialize($serialized);

        $this->assertEquals('int', $newCollection->validator->type);

        $this->assertSame([1, 2, 3], $sequence->toArray());
    }

    public function testJsonSerialize(): void
    {
        $sequence = new Sequence('int');

        $sequence->push(1, 2, 3);

        $json = json_encode($sequence);

        $this->assertSame('[1,2,3]', $json);

        $sequenceFromJson = Sequence::fromJson($json, $sequence->validator->type);

        $this->assertEquals($sequence, $sequenceFromJson);
    }
}
