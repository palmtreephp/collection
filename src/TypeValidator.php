<?php

namespace Palmtree\Collection;

use Palmtree\Collection\Exception\InvalidTypeException;

class TypeValidator
{
    /** @var string */
    protected $type;

    /**
     * An array of primitive types which are valid values for $this->type.
     * The keys are values returned by gettype() and the values are arrays
     * of aliases for that type which can be passed to $this->setType().
     *
     * @var array
     */
    protected $typeMap = [
        'boolean'  => ['boolean', 'bool'],
        'integer'  => ['integer', 'int'],
        'double'   => ['double', 'float'],
        'string'   => ['string'],
        'array'    => ['array'],
        'object'   => ['object'],
        'resource' => ['resource'],
    ];

    public function __construct($type = null)
    {
        $this->setType($type);
    }

    /**
     * Sets the type all items in the collection must be. Can be a primitive type or class name.
     *
     * @see $typeMap for valid primitive types.
     *
     * @param mixed $type
     *
     * @return TypeValidator
     */
    public function setType($type)
    {
        if (!is_null($type) && !is_string($type)) {
            throw new \InvalidArgumentException('Type must be a string');
        }

        $this->type = $type;

        return $this;
    }

    /**
     * Returns the type for this collection.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getTypeMap()
    {
        return $this->typeMap;
    }

    /**
     * @param array $typeMap
     * @return TypeValidator
     */
    public function setTypeMap(array $typeMap)
    {
        $this->typeMap = $typeMap;
        return $this;
    }

    /**
     * Returns whether the given item is a valid type.
     *
     * @param mixed $item
     *
     * @throws InvalidTypeException
     */
    public function validate($item)
    {
        $expected = $this->getType();
        if (!$expected || !is_string($expected)) {
            return;
        }

        $actual = (is_object($item)) ? get_class($item) : gettype($item);

        $valid = false;
        if ((class_exists($expected) || interface_exists($expected)) && $item instanceof $expected) {
            $valid = true;
        } elseif (isset($this->typeMap[$actual]) && in_array($expected, $this->typeMap[$actual])) {
            $valid = true;
        }

        if (!$valid) {
            throw new InvalidTypeException($expected, $actual);
        }
    }
}
