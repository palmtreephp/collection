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
    public function __construct($expected, $actual)
    {
        $this->message = sprintf('Item must be of type %s. %s given', $expected, $actual);
    }
}
