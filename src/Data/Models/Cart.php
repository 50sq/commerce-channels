<?php

namespace FiftySq\Commerce\Data\Models;

use FiftySq\Commerce\Commerce;
use FiftySq\Commerce\Data\Concerns\HasUuid;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cart extends Model
{
    use HasUuid;
    use SoftDeletes;

    protected $casts = [
        'abandoned_at' => 'datetime',
    ];

    public function billingAddress()
    {
        return $this->belongsTo(Address::class, Commerce::prefix('billing_address_id'));
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, $this->prefix('customer_id'), 'uuid');
    }

    public function items()
    {
        return $this->hasMany(LineItem::class, $this->prefix('cart_id'));
    }

    public function order()
    {
        return $this->belongsTo(Order::class, $this->prefix('order_id'));
    }

    public function shippingAddress()
    {
        return $this->belongsTo(Address::class, Commerce::prefix('shipping_address_id'));
    }
}
