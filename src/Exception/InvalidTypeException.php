<?php

namespace Palmtree\Collection\Exception;

class InvalidTypeException extends \Exception
{
    public function __construct(string $expected, string $actual)
    {
        $this->message = "Element must be of type $expected. $actual given";
    }
}
