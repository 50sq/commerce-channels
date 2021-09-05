<?php

namespace FiftySq\Commerce\Data\Models;

use FiftySq\Commerce\Commerce;
use FiftySq\Commerce\Data\Concerns\HandlesSchemaNames;
use Illuminate\Database\Eloquent\Model as Eloquent;

abstract class Model extends Eloquent
{
    use HandlesSchemaNames;

    protected $guarded = [];

    /**
     * Prefix string with Commerce prefix.
     *
     * @param $string
     * @return string
     */
    protected function prefix($string): string
    {
        return Commerce::prefix($string);
    }
}
