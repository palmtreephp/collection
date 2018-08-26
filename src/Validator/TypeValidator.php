<?php

namespace Palmtree\Collection\Validator;

use Palmtree\Collection\Exception\InvalidTypeException;

class TypeValidator
{
    /** @var string|null */
    protected $type;

    /**
     * Array of short-hand types which can be used as the value of $this->type as well as any value returned
     * by {@link http://php.net/manual/en/function.gettype.php gettype()}. Keys are the short-hand value
     * and values are those returned by gettype().
     *
     * @var array
     */
    protected $typeMap = [
        'bool'     => 'boolean',
        'int'      => 'integer',
        'float'    => 'double',
        'string'   => 'string',
        'array'    => 'array',
        'object'   => 'object',
        'resource' => 'resource',
    ];

    public function __construct(?string $type = null)
    {
        $this->setType($type);
    }

    /**
     * Sets the type all items in the collection must be. Can be a primitive type or class name.
     *
     * @see $typeMap for valid primitive types.
     *
     * @param string|null $type
     *
     * @return TypeValidator
     */
    public function setType(?string $type): TypeValidator
    {
        if (!is_null($type) &&
            !isset($this->typeMap[$type]) && !in_array($type, $this->typeMap) &&
            !class_exists($type) && !interface_exists($type)
        ) {
            $validTypes = [];
            foreach ($this->getTypeMap() as $key => $value) {
                $validTypes[] = $key;
                if ($value !== $key) {
                    $validTypes[] = $value;
                }
            }

            $message = "Invalid type '$type'. Must be either NULL, one of ";
            $message .= implode(', ', $validTypes);
            $message .= ' or a fully qualified class name or interface';

            throw new \InvalidArgumentException($message);
        }

        $this->type = $type;

        return $this;
    }

    /**
     * Returns the type for this collection.
     *
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getTypeMap(): array
    {
        return $this->typeMap;
    }

    /**
     * @param array $typeMap
     * @return TypeValidator
     */
    public function setTypeMap(array $typeMap): TypeValidator
    {
        $this->typeMap = $typeMap;
        return $this;
    }

    /**
     * Returns whether the given item is a valid type.
     *
     * @param mixed $element
     *
     * @return bool
     * @throws InvalidTypeException
     */
    public function validate($element): bool
    {
        $expected = $this->getType();
        if (!$expected) {
            return true;
        }

        $actual = is_object($element) ? get_class($element) : gettype($element);

        if (!$this->isValid($element, $expected, $actual)) {
            throw new InvalidTypeException($expected, $actual);
        }

        return true;
    }

    /**
     * @param mixed  $element  The item to check.
     * @param string $expected The expected type $element should be.
     * @param string $actual   The actual type of $element.
     * @return bool
     */
    public function isValid($element, string $expected, string $actual): bool
    {
        if ($this->isInstanceOf($element, $expected) || $this->getMappedType($expected) === $actual) {
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
    protected function isInstanceOf($thing, string $class): bool
    {
        return (class_exists($class) || interface_exists($class)) && $thing instanceof $class;
    }

    /**
     * @param string $type
     * @return string
     */
    protected function getMappedType(string $type): string
    {
        return $this->typeMap[$type] ?? $type;
    }
}
