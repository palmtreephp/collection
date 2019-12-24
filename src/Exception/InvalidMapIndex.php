<?php

namespace Palmtree\Collection\Exception;

class InvalidMapIndex extends \Exception
{
    public function __construct(string $id)
    {
        parent::__construct("Map index '$id' not found");
    }
}
