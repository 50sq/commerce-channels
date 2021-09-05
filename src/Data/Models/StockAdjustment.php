<?php

namespace FiftySq\Commerce\Data\Models;

class StockAdjustment extends Model
{
    const REASON_RECEIVED = 'received';
    const REASON_RETURNED = 'returned';
    const REASON_CANCELED = 'canceled';
    const REASON_SOLD = 'sold';
    const REASON_MISSING = 'missing';
    const REASON_DAMAGED = 'damaged';

    public static array $reasons = [
        self::REASON_RECEIVED,
        self::REASON_RETURNED,
        self::REASON_CANCELED,
        self::REASON_SOLD,
        self::REASON_MISSING,
        self::REASON_DAMAGED,
    ];

    protected $casts = [
        'quantity' => 'integer',
        'level' => 'integer',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, $this->prefix('order_id'));
    }

    public function variants()
    {
        return $this->belongsTo(Variant::class, $this->prefix('variant_id'));
    }
}
