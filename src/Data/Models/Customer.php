<?php

namespace FiftySq\Commerce\Data\Models;

/*
 * Customers are created for orders, and *can* be associated to other models in the system,
 * but it is not required (ie. guest customers)
 */

use FiftySq\Commerce\Data\Concerns\HasAddresses;
use FiftySq\Commerce\Data\Concerns\HasUuid;

class Customer extends Model
{
    use HasAddresses;
    use HasUuid;

    public function abandonedCarts()
    {
        return $this->hasMany(Cart::class, $this->prefix('customer_id'), 'uuid')
            ->whereNotNull('abandoned_at');
    }

    public function cart()
    {
        return $this->hasOne(Cart::class, $this->prefix('customer_id'), 'uuid')
            ->ofMany(['id' => 'max'], fn ($query) => $query->whereNull('abandoned_at'));
    }

    public function pendingOrder()
    {
        return $this->hasOne(PendingOrder::class, $this->prefix('customer_id'), 'uuid')
            ->ofMany(['id' => 'max'], fn ($query) => $query->whereNull('abandoned_at'));
    }

    public function orders()
    {
        return $this->hasMany(Order::class, $this->prefix('customer_id'));
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class, $this->prefix('customer_id'));
    }

    public function user()
    {
        return $this->belongsTo(config('commerce.models.customers'), 'user_id');
    }
}
