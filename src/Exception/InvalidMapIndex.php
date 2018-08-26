<?php

namespace Palmtree\Collection\Exception;

class InvalidMapIndex extends \Exception
{
    /**
     * InvalidMapIndex constructor.
     *
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->message = "Map index '$id' not found";
    }
}
