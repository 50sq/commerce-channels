<?php

namespace FiftySq\Commerce\Data;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use ReflectionClass;

abstract class DataObject implements Arrayable, Jsonable
{
    public function toArray(): array
    {
        $reflection = new ReflectionClass($this);

        $output = [];

        foreach ($reflection->getProperties() as $property) {
            $property->setAccessible(true);
            $output[$property->getName()] = $this->{$property->getName()};
        }

        return $output;
    }

    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), 0);
    }
}
