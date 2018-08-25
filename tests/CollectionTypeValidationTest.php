<?php

namespace Palmtree\Collection\Test;

use Palmtree\Collection\Map;
use Palmtree\Collection\Sequence;
use PHPUnit\Framework\TestCase;

class CollectionTypeValidationTest extends TestCase
{
    /** @expectedException \Palmtree\Collection\Exception\InvalidTypeException */
    public function testInvalidObjectType()
    {
        $collection = new Sequence(\stdClass::class);
        $collection->push(1);
    }

    public function testValidObjectType()
    {
        $collection = new Map(\stdClass::class);

        $this->assertTrue($collection->validate(new \stdClass()));
    }

    public function testValidScalarTypes()
    {
        $collection = new Map('string');

        $this->assertTrue($collection->validate('Foo'));

        $collection = new Map('float');

        $this->assertTrue($collection->validate(0.1));

        $collection = new Map('bool');

        $this->assertTrue($collection->validate(true));
    }

    /** @expectedException \Palmtree\Collection\Exception\InvalidTypeException */
    public function testInvalidScalarType()
    {
        $collection = new Sequence('string');
        $collection->push(1);
    }

    /** @expectedException \InvalidArgumentException */
    public function testInvalidTypeValue()
    {
        new Map(1);
    }
}
