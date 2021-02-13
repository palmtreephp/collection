<?php

namespace Palmtree\Collection\Test;

use Palmtree\Collection\CollectionInterface;
use Palmtree\Collection\Exception\InvalidMapIndex;
use Palmtree\Collection\Map;
use Palmtree\Collection\Test\Fixture\Foo;
use Palmtree\Collection\Test\Fixture\FooInterface;
use PHPUnit\Framework\TestCase;

class MapTest extends TestCase
{
    public function testSetGet()
    {
        $map = new Map();

        $map->set('foo', 'Bar');

        $p = $map->get('foo');

        $z = count($p);

        $this->assertSame('Bar', $map->get('foo'));
    }

    public function testAdd()
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

    public function testRemoveElement()
    {
        $map = new Map();

        $object = new \stdClass();
        $map->set('foo', $object);

        $this->assertTrue($map->has($object));

        $map->removeElement($object);

        $this->assertFalse($map->has($object));
    }

    public function testRemove()
    {
        $map = new Map(\stdClass::class);

        $object      = new \stdClass();
        $object->foo = 'bar';

        $map->set('some_object', $object);

        $map->addIndex('foo', function (\stdClass $element) {
            return $element->foo;
        });

        $map->remove('some_object');

        $this->assertFalse($map->has('some_object'));
        $this->assertNull($map->getBy('foo', 'bar'));
    }

    public function testHas()
    {
        $map = new Map();

        $map->add([1, 2, 3]);

        $this->assertTrue($map->has(2));
    }

    public function testHasKey()
    {
        $map = new Map();

        $map
            ->set('foo', 'Bar')
            ->set('baz', null);

        $this->assertTrue($map->hasKey('foo'));
        $this->assertTrue($map->hasKey('baz'));
    }

    public function testClear()
    {
        $map = new Map(\stdClass::class);

        $object      = new \stdClass();
        $object->foo = 'bar';

        $map->addIndex('foo', function (\stdClass $element) {
            return $element->foo;
        });

        $map->set('some_object', $object);

        $map->clear();

        $this->assertTrue($map->isEmpty());
        $this->assertNull($map->getBy('foo', 'bar'));
    }

    public function testIsEmpty()
    {
        $map = new Map();

        $map
            ->set('foo', 'Bar')
            ->set('baz', null);

        $this->assertFalse($map->isEmpty());

        $map->clear();

        $this->assertTrue($map->isEmpty());
    }

    public function testFirstLast()
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

    public function testFirstLastKeys()
    {
        $map = new Map();

        $map
            ->set('foo', true)
            ->set('bar', true)
            ->set('baz', true);

        $this->assertSame('foo', $map->firstKey());
        $this->assertSame('baz', $map->lastKey());
    }

    public function testKeys()
    {
        $map = new Map();

        $map
            ->set('foo', 'Bar')
            ->set('baz', null);

        $this->assertSame(['foo', 'baz'], $map->keys()->toArray());
    }

    public function testValues()
    {
        $map = new Map();

        $objectOne = new \stdClass();
        $objectTwo = new \stdClass();

        $map
            ->set('foo', $objectOne)
            ->set('baz', $objectTwo);

        $this->assertSame([$objectOne, $objectTwo], $map->values()->toArray());
    }

    public function testIterator()
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

    public function testSerialization()
    {
        $map = new Map('int');

        $map
            ->set('foo', 1)
            ->set('bar', 2)
            ->set('baz', 3);

        $serialized = serialize($map);

        /** @var Map $newMap */
        $newMap = unserialize($serialized);

        $this->assertEquals('int', $newMap->getValidator()->getType());

        $expected = [
            'foo' => 1,
            'bar' => 2,
            'baz' => 3,
        ];

        $this->assertSame($expected, $map->toArray());
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
        $map = new Map('int');

        $map
            ->set('foo', 1)
            ->set('bar', 2)
            ->set('baz', 3);

        $filtered = $map->filter(function ($element, $key) {
            return $key !== 'foo';
        });

        $this->assertNotSame($map, $filtered);
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
        }, null);

        $this->assertSame(4, $mapped['bar']);
    }

    public function testSome()
    {
        $map = new Map();

        $map
            ->set('foo', false)
            ->set('bar', true)
            ->set('baz', false);

        $this->assertTrue($map->some(function ($element) {
            return $element === true;
        }));
    }

    public function testIndex()
    {
        $map = new Map(FooInterface::class);

        $foo  = new Foo('test');
        $foo2 = new Foo('test2');

        $map->set('foo', $foo);
        $map->set('foo2', $foo2);

        $map->addIndex('bar', function (Foo $element) {
            return $element->getBar();
        });

        $this->assertSame($foo, $map->getBy('bar', 'test'));
        $this->assertSame($foo2, $map->getBy('bar', 'test2'));
    }

    public function testInvalidIndex()
    {
        $this->expectException(InvalidMapIndex::class);

        $map = new Map();

        $map->getBy('blah', 1);
    }

    public function testRemoveIndex()
    {
        $this->expectException(InvalidMapIndex::class);

        $map = new Map();

        $map->set('bar', 'baz');

        $map->addIndex('foo', function () {
        });

        $map->removeIndex('foo');

        $map->getBy('foo', 'bar');
    }

    public function testJsonSerialize()
    {
        $map = new Map('int');

        $map
            ->set('foo', 1)
            ->set('bar', 2)
            ->set('baz', 3);

        $json = json_encode($map);

        $this->assertSame('{"foo":1,"bar":2,"baz":3}', $json);

        $mapFromJson = Map::fromJson($json, $map->getValidator()->getType());

        $this->assertEquals($map, $mapFromJson);
    }
}
