<?php

namespace FiftySq\Commerce\Data\Models;

use FiftySq\Commerce\Commerce;

class LineItem extends Model
{
    protected $casts = [
        'quantity' => 'integer',
        'unit_price_cents' => 'integer',
        'options' => 'json',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, $this->prefix('order_id'));
    }

    public function product()
    {
        return $this->belongsTo(Commerce::sellableModel(), $this->prefix('product_id'));
    }

    public function variant()
    {
        return $this->belongsTo(Variant::class, $this->prefix('variant_id'));
    }
}
