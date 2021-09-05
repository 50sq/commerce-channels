<?php

namespace FiftySq\Commerce\Data\Models;

use FiftySq\Commerce\Commerce;
use FiftySq\Commerce\Data\Concerns\HasSku;
use FiftySq\Commerce\Data\Concerns\HasUuid;
use FiftySq\Commerce\Events\VariantCreated;

class Variant extends Model
{
    use HasUuid;
    use HasSku;

    protected $touches = ['product'];

    protected $casts = [
        'unit_price_cents' => 'integer',
    ];

    protected $attributes = [
        'min_quantity' => 1,
    ];

    protected $dispatchesEvents = [
        'created' => VariantCreated::class,
    ];

    public function prices()
    {
        return $this->hasMany(Price::class);
    }

    public function product()
    {
        return $this->belongsTo(Commerce::sellableModel(), Commerce::sellable()->getForeignKey());
    }

    public function stockAdjustments()
    {
        return $this->hasMany(StockAdjustment::class);
    }
}
