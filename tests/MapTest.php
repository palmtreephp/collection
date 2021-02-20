<?php

namespace Palmtree\Collection\Test;

use Palmtree\Collection\Exception\InvalidIndex;
use Palmtree\Collection\Exception\OutOfBoundsException;
use Palmtree\Collection\Map;
use Palmtree\Collection\Test\Fixture\Foo;
use Palmtree\Collection\Test\Fixture\FooInterface;
use PHPUnit\Framework\TestCase;

class MapTest extends TestCase
{
    public function testSetGet(): void
    {
        $map = new Map();

        $map->set('foo', 'Bar');

        $this->assertSame('Bar', $map->get('foo'));
    }

    public function testAdd(): void
    {
        $map = new Map();

        $data = [
            'foo'  => 'Bar',
            'baz'  => 'Bez',
            'bing' => 'Bah',
        ];

        $map->add($data);

        $this->assertSame($data, $map->toArray());
    }

    public function testRemoveElement(): void
    {
        $map = new Map();

        $object = new \stdClass();
        $map->set('foo', $object);

        $this->assertTrue($map->contains($object));

        $map->removeElement($object);

        $this->assertFalse($map->contains($object));
    }

    public function testRemove(): void
    {
        $map = new Map(\stdClass::class);

        $object      = new \stdClass();
        $object->foo = 'bar';

        $map->set('some_object', $object);

        $map->addIndex('foo', fn (\stdClass $element) => $element->foo);

        $map->remove('some_object');

        $this->assertFalse($map->contains('some_object'));

        $this->expectException(OutOfBoundsException::class);

        $map->getBy('foo', 'bar');
    }

    public function testContains(): void
    {
        $map = new Map();

        $object = new \stdClass();

        $map->set('obj', $object);

        $this->assertTrue($map->contains($object));
    }

    public function testContainsKey(): void
    {
        $map = new Map();

        $map
            ->set('foo', 'Bar')
            ->set('baz', null);

        $this->assertTrue($map->containsKey('foo'));
        $this->assertTrue($map->containsKey('baz'));
    }

    public function testClear(): void
    {
        $map = new Map(\stdClass::class);

        $object      = new \stdClass();
        $object->foo = 'bar';

        $map->addIndex('foo', fn (\stdClass $element) => $element->foo);

        $map->set('some_object', $object);

        $map->clear();

        $this->assertTrue($map->isEmpty());

        $this->expectException(OutOfBoundsException::class);
        $map->getBy('foo', 'bar');
    }

    public function testIsEmpty(): void
    {
        $map = new Map();

        $map
            ->set('foo', 'Bar')
            ->set('baz', null);

        $this->assertFalse($map->isEmpty());

        $map->clear();

        $this->assertTrue($map->isEmpty());
    }

    public function testFirstLast(): void
    {
        $map = new Map();

        $objectOne   = new \stdClass();
        $objectTwo   = new \stdClass();
        $objectThree = new \stdClass();

        $map
            ->set('one', $objectOne)
            ->set('two', $objectTwo)
            ->set('three', $objectThree);

        $this->assertSame($objectOne, $map->first());
        $this->assertNotSame($objectTwo, $map->first());

        $this->assertSame($objectThree, $map->last());
        $this->assertNotSame($objectTwo, $map->last());
    }

    public function testFirstLastKeys(): void
    {
        $map = new Map();

        $map
            ->set('foo', true)
            ->set('bar', true)
            ->set('baz', true);

        $this->assertSame('foo', $map->firstKey());
        $this->assertSame('baz', $map->lastKey());
    }

    public function testKeys(): void
    {
        $map = new Map();

        $map
            ->set('foo', 'Bar')
            ->set('baz', null);

        $this->assertSame(['foo', 'baz'], $map->keys()->toArray());
    }

    public function testValues(): void
    {
        $map = new Map();

        $objectOne = new \stdClass();
        $objectTwo = new \stdClass();

        $map
            ->set('foo', $objectOne)
            ->set('baz', $objectTwo);

        $this->assertSame([$objectOne, $objectTwo], $map->values()->toArray());
    }

    public function testIterator(): void
    {
        $map = new Map();

        $map
            ->set('one', 1)
            ->set('two', 2)
            ->set('three', 3);

        $expected = [
            'one'   => 1,
            'two'   => 2,
            'three' => 3,
        ];

        $this->assertSame($expected, iterator_to_array($map));
    }

    public function testSerialization(): void
    {
        $map = new Map('int');

        $map
            ->set('foo', 1)
            ->set('bar', 2)
            ->set('baz', 3);

        $serialized = serialize($map);

        /** @var Map $newMap */
        $newMap = unserialize($serialized);

        $this->assertEquals('int', $newMap->validator->type);

        $expected = [
            'foo' => 1,
            'bar' => 2,
            'baz' => 3,
        ];

        $this->assertSame($expected, $map->toArray());
    }

    public function testFilter(): void
    {
        $map = new Map('int');

        $map
            ->set('foo', 1)
            ->set('bar', 2)
            ->set('baz', 3);

        $filtered = $map->filter(fn ($element) => $element > 1);

        $this->assertNotSame($map, $filtered);
        $this->assertFalse($filtered->containsKey('foo'));

        $map = new Map('bool');

        $map
            ->set('foo', true)
            ->set('bar', false)
            ->set('baz', true);

        $filtered = $map->filter();

        $this->assertNotSame($map, $filtered);
        $this->assertFalse($filtered->containsKey('bar'));
    }

    public function testFilterWithKeys(): void
    {
        $map = new Map('int');

        $map
            ->set('foo', 1)
            ->set('bar', 2)
            ->set('baz', 3);

        $filtered = $map->filter(fn ($element, $key) => $key !== 'foo');

        $this->assertNotSame($map, $filtered);
        $this->assertFalse($filtered->containsKey('foo'));
    }

    public function testMapWithKeys(): void
    {
        $map = new Map();

        $map
            ->set('foo', 1)
            ->set('bar', 2)
            ->set('baz', 3);

        $mapped = $map->map(fn ($element, $key) => $key === 'bar' ? 4 : $element);

        $this->assertSame(4, $mapped['bar']);
    }

    public function testSome(): void
    {
        $map = new Map();

        $map
            ->set('foo', false)
            ->set('bar', true)
            ->set('baz', false);

        $this->assertTrue($map->some(fn ($element) => $element === true));
    }

    public function testIndex(): void
    {
        $map = new Map(FooInterface::class);

        $foo  = new Foo('test');
        $foo2 = new Foo('test2');

        $map->set('foo', $foo);
        $map->set('foo2', $foo2);

        $map->addIndex('bar', fn (Foo $element) => $element->getBar());

        $this->assertSame($foo, $map->getBy('bar', 'test'));
        $this->assertSame($foo2, $map->getBy('bar', 'test2'));
    }

    public function testInvalidIndex(): void
    {
        $this->expectException(InvalidIndex::class);

        $map = new Map();

        $map->getBy('foo', 'bar');
    }

    public function testRemoveIndex(): void
    {
        $this->expectException(InvalidIndex::class);

        $map = new Map();

        $map->set('bar', 'baz');

        $map->addIndex('foo', fn () => '');

        $map->removeIndex('foo');

        $map->getBy('foo', 'bar');
    }

    public function testJsonSerialize(): void
    {
        $map = new Map('int');

        $map
            ->set('foo', 1)
            ->set('bar', 2)
            ->set('baz', 3);

        $json = json_encode($map);

        $this->assertSame('{"foo":1,"bar":2,"baz":3}', $json);

        $mapFromJson = Map::fromJson($json, $map->validator->type);

        $this->assertEquals($map, $mapFromJson);
    }
}
