<?php

namespace Palmtree\Collection\Validator;

use Palmtree\Collection\Exception\InvalidArgumentException;
use Palmtree\Collection\Exception\InvalidTypeException;

class TypeValidator
{
    /** @var string|null */
    private $type;

    /**
     * Array of short-hand types which can be used as the value of $this->type as well as any value returned
     * by {@link http://php.net/manual/en/function.gettype.php gettype()}. Keys are the short-hand value
     * and values are those returned by gettype().
     *
     * @var array
     */
    private $typeMap = [
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
     * Sets the type all elements in the collection must be. Can be a primitive type, class name or interface.
     *
     * @see $typeMap for valid primitive types.
     */
    public function setType(?string $type): self
    {
        if (!$this->isValidType($type)) {
            $validTypes = \implode(', ', $this->getValidTypes());
            throw new InvalidArgumentException("Invalid type '$type'. Must be either NULL, one of $validTypes, or a FQCN or interface");
        }

        $this->type = $type;

        return $this;
    }

    public function isValidType(?string $type): bool
    {
        return
            null === $type ||
            \class_exists($type) ||
            \interface_exists($type) ||
            \in_array($type, $this->getValidTypes());
    }

    public function getValidTypes(): array
    {
        $validTypes = [];
        foreach ($this->getTypeMap() as $key => $value) {
            $validTypes[] = $key;
            if ($value !== $key) {
                $validTypes[] = $value;
            }
        }

        return $validTypes;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getTypeMap(): array
    {
        return $this->typeMap;
    }

    public function setTypeMap(array $typeMap): self
    {
        $this->typeMap = $typeMap;

        return $this;
    }

    /**
     * Returns true if the given element is a valid type. Throws an InvalidTypeException otherwise.
     *
     * @param mixed $element
     *
     * @throws InvalidTypeException
     */
    public function validate($element): bool
    {
        if (!$expectedType = $this->getType()) {
            return true;
        }

        $expectedType = $this->typeMap[$expectedType] ?? $expectedType;
        $actualType   = \gettype($element);

        if (($actualType === 'object' && $element instanceof $expectedType) || $actualType === $expectedType) {
            return true;
        }

        throw new InvalidTypeException($expectedType, $actualType === 'object' ? \get_class($element) : $actualType);
    }
}
