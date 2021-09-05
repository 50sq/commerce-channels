<?php

namespace FiftySq\Commerce\Contracts;

interface ProductResourceContract
{
    /**
     * Create a new resource instance.
     *
     * @param  mixed  ...$parameters
     * @return static
     */
    public static function make(...$parameters): self;

    /**
     * Create a new anonymous resource collection.
     *
     * @param  mixed  $resource
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public static function collection($resource): \Illuminate\Http\Resources\Json\AnonymousResourceCollection;
}
