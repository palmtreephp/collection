<?php

declare(strict_types=1);

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
    public function testInvalidObjectType(): void
    {
        $this->expectException(InvalidTypeException::class);

        $sequence = new Sequence('int');
        $sequence->push(new \stdClass());
    }

    public function testValidObjectType(): void
    {
        $map = new Map(\stdClass::class);

        $this->assertTrue($map->validator->validate(new \stdClass()));
    }

    public function testValidObjectInterfaceType(): void
    {
        $sequence = new Sequence(FooInterface::class);

        $this->assertTrue($sequence->validator->validate(new Foo()));
    }

    public function testValidScalarTypes(): void
    {
        $map = new Map('string');

        $this->assertTrue($map->validator->validate('Foo'));

        $map = new Map('float');

        $this->assertTrue($map->validator->validate(0.1));

        $map = new Map('bool');

        $this->assertTrue($map->validator->validate(true));
    }

    public function testInvalidScalarType(): void
    {
        $this->expectException(InvalidTypeException::class);

        $sequence = new Sequence('string');
        $sequence->push(1);
    }

    public function testInvalidTypeValue(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $map = new Map('foo');
    }

    public function testNoTypeChecking(): void
    {
        $sequence = new Sequence();
        $sequence->push(1, true, 'Hello World');

        $this->assertSame([1, true, 'Hello World'], $sequence->toArray());
    }
}
