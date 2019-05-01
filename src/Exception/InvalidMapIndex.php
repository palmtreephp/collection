<?php

namespace Palmtree\Collection\Exception;

class InvalidMapIndex extends \Exception
{
    public function __construct(string $id)
    {
        $this->message = "Map index '$id' not found";
    }
}
