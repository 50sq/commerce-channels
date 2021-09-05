<?php

namespace FiftySq\Commerce\Data\Models;

use FiftySq\Commerce\Commerce;
use FiftySq\Commerce\Data\Concerns\HasPublicOrderId;

class Order extends Model
{
    use HasPublicOrderId;

    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING = 'pending';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_PROCESSING = 'processing';
    const STATUS_ONHOLD = 'onhold';
    const STATUS_PARTIAL = 'partial';
    const STATUS_FULFILLED = 'fulfilled';

    protected $casts = [
        'cancelled_at' => 'datetime',
    ];

    protected $attributes = [
        'payment_status' => Payment::STATUS_UNPAID,
        'order_status' => self::STATUS_DRAFT,
    ];

    public function billingAddress()
    {
        return $this->belongsTo(Address::class, Commerce::prefix('billing_address_id'));
    }

    public function cart()
    {
        return $this->hasOne(Cart::class, 'id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, $this->prefix('customer_id'));
    }

    public function discounts()
    {
        return $this->belongsToMany(Discount::class, $this->prefix('discount_order'));
    }

    public function items()
    {
        return $this->hasMany(LineItem::class, $this->prefix('order_id'));
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, $this->prefix('order_id'));
    }

    public function returns()
    {
        return $this->hasMany(ProductReturn::class, $this->prefix('order_id'));
    }

    public function shippingAddress()
    {
        return $this->belongsTo(Address::class, Commerce::prefix('shipping_address_id'));
    }
}
