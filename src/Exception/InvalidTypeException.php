<?php

namespace Palmtree\Collection\Exception;

class InvalidTypeException extends \Exception
{
    /**
     * InvalidTypeException constructor.
     *
     * @param string $expected
     * @param string $actual
     */
    public function __construct(string $expected, string $actual)
    {
        $this->message = sprintf('Element must be of type %s. %s given', $expected, $actual);
    }
}
