<?php

namespace Palmtree\Collection\Test;

use Palmtree\Collection\Collection;
use PHPUnit\Framework\TestCase;

class CollectionTypeValidationTest extends TestCase
{
    /** @expectedException \Palmtree\Collection\Exception\InvalidTypeException */
    public function testInvalidObjectType()
    {
        $collection = new Collection(\stdClass::class);
        $collection->push(1);
    }

    public function testValidObjectType()
    {
        $collection = new Collection(\stdClass::class);
        $collection->push(new \stdClass());
    }

    public function testValidScalarType()
    {
        $collection = new Collection('string');
        $collection->push('Foo');
    }

    /** @expectedException \Palmtree\Collection\Exception\InvalidTypeException */
    public function testInvalidScalarType()
    {
        $collection = new Collection('string');
        $collection->push(1);
    }

    /** @expectedException \InvalidArgumentException */
    public function testInvalidTypeValue()
    {
        $collection = new Collection(1);
    }
}
