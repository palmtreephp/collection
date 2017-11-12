<?php

namespace Palmtree\Collection\Validator;

use Palmtree\Collection\Exception\InvalidTypeException;

class TypeValidator
{
    /** @var string */
    protected $type;

    /**
     * Array of short-hand types which can be used as the value of $this->type as well as any value returned
     * by {@link http://php.net/manual/en/function.gettype.php gettype()}. Keys are the short-hand value
     * and values are those returned by gettype().
     *
     * @var array
     */
    protected $typeMap = [
        'bool'  => 'boolean',
        'int'   => 'integer',
        'float' => 'double',
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
        if (!$expected) {
            return;
        }

        $actual = is_object($item) ? get_class($item) : gettype($item);

        if (!$this->isValid($item, $expected, $actual)) {
            throw new InvalidTypeException($expected, $actual);
        }
    }

    /**
     * @param mixed  $item     The item to check.
     * @param string $expected The expected type $item should be.
     * @param string $actual   The actual type of $item.
     * @return bool
     */
    public function isValid($item, $expected, $actual)
    {
        if ($this->isInstanceOf($item, $expected) || $this->getMappedType($expected) === $actual) {
            return true;
        }

        return false;
    }

    /**
     * Returns whether an object is an instance of the given class or interface
     *
     * @param object $thing
     * @param string $class Fully qualified class or interface name.
     * @return bool
     */
    protected function isInstanceOf($thing, $class)
    {
        return (class_exists($class) || interface_exists($class)) && $thing instanceof $class;
    }

    /**
     * @param string $type
     * @return string
     */
    protected function getMappedType($type)
    {
        return isset($this->typeMap[$type]) ? $this->typeMap[$type] : $type;
    }
}
