<?php

namespace FiftySq\Commerce\Data\Models;

class Payment extends Model
{
    const STATUS_NO_PAYMENT_REQUIRED = 'no_payment_required';
    const STATUS_PAID = 'paid';
    const STATUS_UNPAID = 'unpaid';

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, $this->prefix('order_id'));
    }
}
