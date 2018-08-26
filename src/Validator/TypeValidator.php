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

    /**
     * TypeValidator constructor.
     * @param string|null $type
     */
    public function __construct(?string $type = null)
    {
        $this->setType($type);
    }

    /**
     * Sets the type all elements in the collection must be. Can be a primitive type, class name or interface.
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
     * Returns whether the given element is a valid type.
     *
     * @param mixed $element
     *
     * @return bool
     * @throws InvalidTypeException
     */
    public function validate($element): bool
    {
        $expectedType = $this->getType();
        if (!$expectedType) {
            return true;
        }

        $expectedType = $this->typeMap[$expectedType] ?? $expectedType;
        $actualType   = is_object($element) ? get_class($element) : gettype($element);

        if ((is_object($element) && $element instanceof $expectedType) || $actualType === $expectedType) {
            return true;
        }

        throw new InvalidTypeException($expectedType, $actualType);
    }
}
