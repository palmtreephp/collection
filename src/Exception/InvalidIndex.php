<?php

declare(strict_types=1);

namespace Palmtree\Collection\Exception;

class InvalidIndex extends \Exception
{
    public function __construct(string $id)
    {
        parent::__construct("Index '$id' not found");
    }
}
