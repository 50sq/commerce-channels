<?php

namespace FiftySq\Commerce\Data\Models;

use FiftySq\Commerce\Data\Concerns\HasUuid;

class Price extends Model
{
    use HasUuid;

    const TYPE_RECURRING = 'recurring';
    const TYPE_ONE_TIME = 'one_time';

    const TAX_BEHAVIOR_INCLUSIVE = 'inclusive';
    const TAX_BEHAVIOR_EXCLUSIVE = 'exclusive';

    protected $casts = [
        'is_active' => 'boolean',
        'unit_amount' => 'integer',
    ];

    protected $attributes = [
        'type' => self::TYPE_RECURRING,
        'tax_behavior' => self::TAX_BEHAVIOR_EXCLUSIVE,
    ];

    public function variant()
    {
        return $this->belongsTo(Variant::class);
    }
}
