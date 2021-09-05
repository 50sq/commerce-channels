<?php

namespace App\Models;

use FiftySq\Commerce\Sellable;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use Sellable;

    protected $guarded = [];
}
