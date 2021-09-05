<?php

namespace FiftySq\Commerce\Data\Models;

class Discount extends Model
{
    const TYPE_FIXED_AMOUNT = 'fixed_amount';
    const TYPE_PERCENTAGE = 'percentage';

    public function orders()
    {
        return $this->belongsToMany(Order::class, $this->prefix('order_id'));
    }
}
