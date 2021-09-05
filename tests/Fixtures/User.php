<?php

namespace Tests\Fixtures;

use FiftySq\Commerce\Data\Models\Customer;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $guarded = [];

    protected function customer()
    {
        return $this->belongsTo(Customer::class, 'user_id');
    }
}
