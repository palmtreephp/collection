<?php

namespace Palmtree\Collection\Test;

use Palmtree\Collection\Exception\InvalidArgumentException;
use Palmtree\Collection\Exception\InvalidTypeException;
use Palmtree\Collection\Map;
use Palmtree\Collection\Sequence;
use Palmtree\Collection\Test\Fixture\Foo;
use Palmtree\Collection\Test\Fixture\FooInterface;
use PHPUnit\Framework\TestCase;

class TypeValidationTest extends TestCase
{
    public function testInvalidObjectType()
    {
        $this->expectException(InvalidTypeException::class);

        $sequence = new Sequence('int');
        $sequence->push(new \stdClass());
    }

    public function testValidObjectType()
    {
        $map = new Map(\stdClass::class);

        $this->assertTrue($map->getValidator()->validate(new \stdClass()));
    }

    public function testValidObjectInterfaceType()
    {
        $sequence = new Sequence(FooInterface::class);

        $this->assertTrue($sequence->getValidator()->validate(new Foo()));
    }

    public function testValidScalarTypes()
    {
        $map = new Map('string');

        $this->assertTrue($map->getValidator()->validate('Foo'));

        $map = new Map('float');

        $this->assertTrue($map->getValidator()->validate(0.1));

        $map = new Map('bool');

        $this->assertTrue($map->getValidator()->validate(true));
    }

    public function testInvalidScalarType()
    {
        $this->expectException(InvalidTypeException::class);

        $sequence = new Sequence('string');
        $sequence->push(1);
    }

    public function testInvalidTypeValue()
    {
        $this->expectException(InvalidArgumentException::class);

        new Map(1);
    }

    public function testTypeMap()
    {
        $map = new Map();

        $map->getValidator()->setTypeMap(['bool' => 'boolean']);

        $this->assertSame(['bool' => 'boolean'], $map->getValidator()->getTypeMap());
    }
}
