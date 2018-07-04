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

        $this->assertTrue($collection->getValidator()->validate(new \stdClass()));
    }

    public function testValidScalarTypes()
    {
        $collection = new Collection('string');

        $this->assertTrue($collection->getValidator()->validate('Foo'));

        $collection = new Collection('float');

        $this->assertTrue($collection->getValidator()->validate(0.1));

        $collection = new Collection('bool');

        $this->assertTrue($collection->getValidator()->validate(true));
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
        new Collection(1);
    }
}
