<?php

namespace Palmtree\Collection\Test;

use Palmtree\Collection\Map;
use Palmtree\Collection\Sequence;
use Palmtree\Collection\Test\Fixture\Foo;
use Palmtree\Collection\Test\Fixture\FooInterface;
use PHPUnit\Framework\TestCase;

class TypeValidationTest extends TestCase
{
    /** @expectedException \Palmtree\Collection\Exception\InvalidTypeException */
    public function testInvalidObjectType()
    {
        $sequence = new Sequence('int');
        $sequence->push(new \stdClass());
    }

    public function testValidObjectType()
    {
        $map = new Map(\stdClass::class);

        $this->assertTrue($map->validate(new \stdClass()));
    }

    public function testValidObjectInterfaceType()
    {
        $sequence = new Sequence(FooInterface::class);

        $this->assertTrue($sequence->validate(new Foo()));
    }

    public function testValidScalarTypes()
    {
        $map = new Map('string');

        $this->assertTrue($map->validate('Foo'));

        $map = new Map('float');

        $this->assertTrue($map->validate(0.1));

        $map = new Map('bool');

        $this->assertTrue($map->validate(true));
    }

    /** @expectedException \Palmtree\Collection\Exception\InvalidTypeException */
    public function testInvalidScalarType()
    {
        $sequence = new Sequence('string');
        $sequence->push(1);
    }

    /** @expectedException \InvalidArgumentException */
    public function testInvalidTypeValue()
    {
        new Map(1);
    }

    public function testTypeMap()
    {
        $map = new Map();

        $map->getValidator()->setTypeMap(['bool' => 'boolean']);

        $this->assertSame(['bool' => 'boolean'], $map->getValidator()->getTypeMap());
    }
}
