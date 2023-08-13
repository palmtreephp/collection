<?php

declare(strict_types=1);

namespace Palmtree\EasyCollection\Test;

use function Palmtree\EasyCollection\c;

use Palmtree\EasyCollection\Collection;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    public function testAdd(): void
    {
        $obj1 = new \stdClass();
        $obj2 = new \stdClass();

        $collection = new Collection([$obj1]);
        $collection->add($obj2);

        $this->assertSame($obj2, $collection->last());
        $this->assertSame($obj1, $collection->get(0));
        $this->assertSame($obj2, $collection->get(1));
    }

    public function testSet(): void
    {
        $obj1 = new \stdClass();
        $obj2 = new \stdClass();

        $collection = new Collection(['foo' => $obj1]);
        $collection->set('bar', $obj2);

        $this->assertSame($obj2, $collection->get('bar'));
    }

    public function testGet(): void
    {
        $obj1 = new \stdClass();
        $obj2 = new \stdClass();

        $collection = new Collection(['foo' => $obj1, 'bar' => $obj2]);

        $this->assertSame($obj1, $collection->get('foo'));
        $this->assertSame($obj2, $collection->get('bar'));
    }

    public function testKey(): void
    {
        $obj1 = new \stdClass();
        $obj2 = new \stdClass();

        $collection = new Collection(['foo' => $obj1, 'bar' => $obj2]);

        $this->assertSame('foo', $collection->key($obj1));
        $this->assertSame('bar', $collection->key($obj2));
    }

    public function testRemoveElement(): void
    {
        $obj1 = new \stdClass();
        $obj2 = new \stdClass();

        $collection = new Collection([$obj1, $obj2]);

        $result = $collection->removeElement($obj1);

        $this->assertTrue($result);
        $this->assertFalse($collection->contains($obj1));
        $this->assertTrue($collection->contains($obj2));

        $result = $collection->removeElement(new \stdClass());
        $this->assertFalse($result);
    }

    public function testRemove(): void
    {
        $obj1 = new \stdClass();
        $obj2 = new \stdClass();

        $collection = new Collection(['foo' => $obj1, 'bar' => $obj2]);

        $removed = $collection->remove('foo');

        $this->assertFalse($collection->contains($obj1));
        $this->assertTrue($collection->contains($obj2));
        $this->assertSame($removed, $obj1);

        $removed = $collection->remove('noop');
        $this->assertNull($removed);
    }

    public function testKeys(): void
    {
        $obj1 = new \stdClass();
        $obj2 = new \stdClass();

        $collection = new Collection(['foo' => $obj1, 'bar' => $obj2]);

        $this->assertSame(['foo', 'bar'], $collection->keys()->toArray());
    }

    public function testValues(): void
    {
        $obj1 = new \stdClass();
        $obj2 = new \stdClass();

        $collection = new Collection(['foo' => $obj1, 'bar' => $obj2]);

        $this->assertSame([$obj1, $obj2], $collection->values()->toArray());
    }

    public function testFirstKey(): void
    {
        $obj1 = new \stdClass();
        $obj2 = new \stdClass();

        $collection = new Collection(['foo' => $obj1, 'bar' => $obj2]);

        $this->assertSame('foo', $collection->firstKey());

        $collection->clear();

        $this->assertNull($collection->firstKey());
    }

    public function testLastKey(): void
    {
        $obj1 = new \stdClass();
        $obj2 = new \stdClass();
        $obj3 = new \stdClass();

        $collection = new Collection(['foo' => $obj1, 'bar' => $obj2, 'baz' => $obj3]);

        $this->assertSame('baz', $collection->lastKey());

        $collection->clear();

        $this->assertNull($collection->lastKey());
    }

    public function testFirst(): void
    {
        $obj1 = new \stdClass();
        $obj2 = new \stdClass();

        $collection = new Collection([$obj1, $obj2]);

        $this->assertSame($obj1, $collection->first());

        $collection->clear();

        $this->assertNull($collection->first());
    }

    public function testLast(): void
    {
        $obj1 = new \stdClass();
        $obj2 = new \stdClass();
        $obj3 = new \stdClass();

        $collection = new Collection([$obj1, $obj2, $obj3]);

        $this->assertSame($obj3, $collection->last());

        $collection->clear();

        $this->assertNull($collection->last());
    }

    public function testEach(): void
    {
        $collection = new Collection([2, 4, 8, 16]);

        /** @var Collection<int, int> $newCollection */
        $newCollection = new Collection();

        $collection->each(fn ($value) => $newCollection->add($value * 2));

        $this->assertSame([4, 8, 16, 32], $newCollection->toArray());

        $collection = new Collection(['foo' => 'bar', 'baz' => 'qux']);

        $keys = ['foo', 'baz'];

        $collection->each(function ($_, $key, $loopIndex) use ($keys): void {
            $this->assertSame($keys[$loopIndex], $key);
        });
    }

    public function testBreakingFromEach(): void
    {
        $collection = new Collection([2, 4, 8, 16]);
        $newCollection = new Collection();
        $collection->each(function ($value) use ($newCollection) {
            if ($value === 8) {
                return false;
            }

            $newCollection->add($value * 2);
        });

        $this->assertSame([4, 8], $newCollection->toArray());
    }

    public function testFind(): void
    {
        $obj1 = new \stdClass();
        $obj2 = new \stdClass();
        $obj3 = new \stdClass();

        $obj1->foo = 'noop';
        $obj2->foo = 'bar';
        $obj3->foo = 'bar';

        $collection = new Collection([$obj1, $obj2, $obj3]);

        $this->assertSame($obj2, $collection->find(fn ($o) => $o->foo === 'bar'));
        $this->assertNull($collection->find(fn ($o) => $o->foo === 'qux'));
    }

    public function testFilter(): void
    {
        $obj1 = new \stdClass();
        $obj2 = new \stdClass();
        $obj3 = new \stdClass();

        $obj1->foo = 'noop';
        $obj2->foo = 'bar';
        $obj3->foo = 'bar';

        $collection = (new Collection([$obj1, $obj2, $obj3]))->filter(fn ($o) => $o->foo === 'bar');

        $this->assertFalse($collection->contains($obj1));
        $this->assertTrue($collection->contains($obj2));
        $this->assertTrue($collection->contains($obj3));
    }

    public function testFilterWithNoCallback(): void
    {
        $collection = new Collection([true, false, true, null]);

        $this->assertSame([0 => true, 2 => true], $collection->filter()->toArray());
    }

    public function testMap(): void
    {
        $obj1 = new \stdClass();
        $obj2 = new \stdClass();
        $obj3 = new \stdClass();

        $obj1->foo = 'noop';
        $obj2->foo = 'bar';
        $obj3->foo = 'bar';

        $collection = (new Collection([$obj1, $obj2, $obj3]))->map(fn ($o) => $o->foo);

        $this->assertSame(['noop', 'bar', 'bar'], $collection->toArray());
    }

    public function testReduce(): void
    {
        $collection = new Collection([10, 20, 30]);

        $this->assertSame(60, $collection->reduce(fn ($val, $acc) => $val + $acc));
    }

    public function testContains(): void
    {
        $obj1 = new \stdClass();
        $obj2 = new \stdClass();

        $collection = new Collection([$obj1, $obj2]);

        $this->assertTrue($collection->contains($obj1));
        $this->assertTrue($collection->contains($obj2));
    }

    public function testContainsKey(): void
    {
        $obj1 = new \stdClass();
        $obj2 = new \stdClass();

        $collection = new Collection(['foo' => $obj1, 'bar' => $obj2]);

        $this->assertTrue($collection->containsKey('foo'));
        $this->assertTrue($collection->containsKey('bar'));

        $collection = new Collection(['foo' => null, 'bar' => null]);
        $this->assertTrue($collection->containsKey('foo'));
        $this->assertTrue($collection->containsKey('bar'));
    }

    public function testClearAndIsEmpty(): void
    {
        $collection = new Collection(['foo', 'bar']);

        $collection->clear();

        $this->assertTrue($collection->isEmpty());
    }

    public function testCount(): void
    {
        $collection = new Collection(['foo', 'bar', 'baz']);

        $this->assertCount(3, $collection);

        $collection->removeElement('bar');

        $this->assertCount(2, $collection);
    }

    public function testSome(): void
    {
        $collection = new Collection(['foo', 'bar', 'baz']);

        $this->assertTrue($collection->some(fn ($v) => $v === 'bar'));
        $this->assertFalse($collection->some(fn ($v) => $v === 'qux'));
    }

    public function testEvery(): void
    {
        $collection = new Collection(['foo', 'bar', 'baz']);

        $this->assertTrue($collection->every(fn ($v) => $v !== 'qux'));
        $this->assertFalse($collection->every(fn ($v) => $v === 'qux'));
    }

    public function testSort(): void
    {
        $collection = new Collection([3, 1, 2, 9, 7]);

        $sorted = $collection->sort()->values();

        $this->assertNotSame($collection, $sorted);

        $this->assertSame([1, 2, 3, 7, 9], $sorted->toArray());
    }

    public function testSortWithStringKeys(): void
    {
        $collection = new Collection(['foo' => 3, 'bar' => 1, 'baz' => 2]);

        $sorted = $collection->sort();

        $this->assertSame(['bar' => 1, 'baz' => 2, 'foo' => 3], $sorted->toArray());
    }

    public function testUSort(): void
    {
        $collection = new Collection([3, 1, 2, 9, 7]);

        $sorted = $collection->usort(fn ($a, $b) => $b <=> $a)->values();

        $this->assertSame([9, 7, 3, 2, 1], $sorted->toArray());
    }

    public function testUSortWithStringKeys(): void
    {
        $collection = new Collection(['foo' => 3, 'bar' => 1, 'baz' => 2]);

        $sorted = $collection->usort(fn ($a, $b) => $a <=> $b);

        $this->assertSame(['bar' => 1, 'baz' => 2, 'foo' => 3], $sorted->toArray());
    }

    public function testKSort(): void
    {
        $collection = new Collection([
            'b' => 'foo',
            'a' => 'bar',
            'd' => 'baz',
            'c' => 'qux',
        ]);

        $sorted = $collection->ksort();

        $this->assertSame(['a', 'b', 'c', 'd'], $sorted->keys()->toArray());
    }

    public function testIsList(): void
    {
        $collection = new Collection(['foo', 'bar', 'baz']);
        $this->assertTrue($collection->isList());

        $collection = new Collection(['foo' => 'bar', 'baz' => 'qux']);
        $this->assertFalse($collection->isList());

        $collection = new Collection([0 => 'foo', 2 => 'bar']);
        $this->assertFalse($collection->isList());

        $collection = new Collection([1 => 'foo', 2 => 'bar']);
        $this->assertFalse($collection->isList());
    }

    public function testArrayAccess(): void
    {
        $collection = new Collection();
        $collection['foo'] = 'bar';
        $collection['baz'] = 'qux';

        $this->assertSame(['foo' => 'bar', 'baz' => 'qux'], $collection->toArray());

        $this->assertTrue(isset($collection['foo']));

        unset($collection['foo']);

        $this->assertFalse($collection->containsKey('foo'));

        $this->assertSame('qux', $collection['baz']);

        $collection = new Collection();
        $collection[] = 'foo';
        $collection[] = 'bar';

        $this->assertSame(['foo', 'bar'], $collection->toArray());
    }

    public function testIterator(): void
    {
        $collection = new Collection(['foo', 'bar', 'baz']);

        $this->assertSame(['foo', 'bar', 'baz'], iterator_to_array($collection));
    }

    public function testShortHandFunction(): void
    {
        $collection = c(['foo' => 'bar', 'baz' => 'qux']);

        $this->assertInstanceOf(Collection::class, $collection);
    }

    public function testPluckWithObjects(): void
    {
        $obj1 = new \stdClass();
        $obj1->id = 1;
        $obj1->name = 'foo';

        $obj2 = new \stdClass();
        $obj2->id = 2;
        $obj2->name = 'bar';

        $obj3 = new \stdClass();
        $obj3->id = 3;
        $obj3->name = 'baz';

        $collection = new Collection([$obj1, $obj2, $obj3]);

        $plucked = $collection->pluck('id');
        $this->assertSame([1, 2, 3], $plucked->toArray());

        $plucked = $collection->pluck('name');
        $this->assertSame(['foo', 'bar', 'baz'], $plucked->toArray());
    }

    public function testPluckWithArrays(): void
    {
        $collection = new Collection([
            ['id' => 1, 'name' => 'foo'],
            ['id' => 2, 'name' => 'bar'],
            ['id' => 3, 'name' => 'baz'],
        ]);

        $plucked = $collection->pluck('id');
        $this->assertSame([1, 2, 3], $plucked->toArray());

        $plucked = $collection->pluck('name');
        $this->assertSame(['foo', 'bar', 'baz'], $plucked->toArray());
    }

    public function testPluckThrowsExceptionOnInvalidData(): void
    {
        $collection = new Collection(['foo', 'bar', 'baz']);

        $this->expectException('LogicException');
        $collection->pluck('id');
    }

    public function testSlice(): void
    {
        $collection = new Collection(['foo', 'bar', 'baz']);

        $sliced = $collection->slice(0, 2);
        $this->assertSame(['foo', 'bar'], $sliced->toArray());

        $sliced = $collection->slice(1, 1);
        $this->assertSame(['bar'], $sliced->toArray());
    }

    public function testDiff(): void
    {
        $collection = new Collection(['foo', 'bar', 'baz']);
        $collection2 = new Collection(['bar']);

        $diff = $collection->diff(['foo'], $collection2);

        $this->assertSame([2 => 'baz'], $diff->toArray());
    }

    public function testIntersect(): void
    {
        $collection = new Collection(['foo', 'bar', 'baz']);
        $collection2 = new Collection(['bar', 'baz']);

        $intersection = $collection->intersect($collection2);

        $this->assertSame([1 => 'bar', 2 => 'baz'], $intersection->toArray());
    }

    public function testUnique(): void
    {
        $collection = new Collection(['foo', 'bar', 'baz', 'foo', 'baz']);

        $unique = $collection->unique();

        $this->assertSame(['foo', 'bar', 'baz'], $unique->toArray());
    }

    public function testUniqueStrict(): void
    {
        $obj1 = new \stdClass();
        $obj2 = new \stdClass();

        $collection = new Collection([$obj1, $obj2, $obj1, $obj2]);

        $this->assertSame([$obj1, $obj2], $collection->unique(true)->toArray());
    }

    public function testImplode(): void
    {
        $collection = new Collection(['foo', 'bar', 'baz']);

        $this->assertSame('foo,bar,baz', $collection->implode());
        $this->assertSame('foo bar baz', $collection->implode(' '));
    }

    public function testReverse(): void
    {
        $collection = new Collection(['foo', 'bar', 'baz']);

        $this->assertSame(['baz', 'bar', 'foo'], $collection->reverse()->toArray());

        $collection = new Collection([1 => 'foo', 2 => 'bar', 3 => 'baz']);
        $this->assertSame([3 => 'baz', 2 => 'bar', 1 => 'foo'], $collection->reverse(true)->toArray());
    }

    public function testFlip(): void
    {
        $collection = new Collection([1 => 'foo', 2 => 'bar', 3 => 'baz']);

        $this->assertSame(['foo' => 1, 'bar' => 2, 'baz' => 3], $collection->flip()->toArray());
    }

    public function testFlatten(): void
    {
        $collection = new Collection([1, 2, [3, 4, [5, 6]]]);
        $this->assertSame([1, 2, 3, 4, 5, 6], $collection->flatten()->toArray());
    }
}
