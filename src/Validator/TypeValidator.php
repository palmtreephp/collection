<?php

namespace Palmtree\Collection\Validator;

use Palmtree\Collection\Exception\InvalidArgumentException;
use Palmtree\Collection\Exception\InvalidTypeException;

class TypeValidator
{
    /** @var string|null */
    private $type;

    private const VALID_PRIMITIVE_TYPES = [
        'boolean' => 'bool',
        'integer' => 'int',
        'double'  => 'float',
        'string'  => 'string',
        'array'   => 'array',
        'object'  => 'object',
    ];

    public function __construct(?string $type = null)
    {
        $this->setType($type);
    }

    /**
     * Returns true if the given element is a valid type.
     *
     * @throws InvalidTypeException If the given element is not a valid type.
     */
    public function validate($element): bool
    {
        if ($this->type === null) {
            return true;
        }

        $actualType = self::VALID_PRIMITIVE_TYPES[\gettype($element)];

        if (($actualType === 'object' && $element instanceof $this->type) || $actualType === $this->type) {
            return true;
        }

        throw new InvalidTypeException($this->type, $actualType === 'object' ? \get_class($element) : $actualType);
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function isValidType(?string $type): bool
    {
        return $type === null ||
            class_exists($type) ||
            interface_exists($type) ||
            \in_array($type, self::VALID_PRIMITIVE_TYPES, true);
    }

    /**
     * Sets the type all elements in the collection must be. Can be a primitive type, class name or interface.
     */
    private function setType(?string $type)
    {
        if (!$this->isValidType($type)) {
            $validTypes = implode(', ', self::VALID_PRIMITIVE_TYPES);
            throw new InvalidArgumentException("Invalid type '$type'. Must be either null, one of $validTypes, or a FQCN or interface");
        }

        $this->type = $type;
    }
}
