<?php

namespace FiftySq\Commerce\Payments\Events;

use FiftySq\Commerce\Data\Models\Order;
use Illuminate\Queue\SerializesModels;

class CheckoutPaymentSucceeded
{
    use SerializesModels;

    public Order $order;

    /**
     * CheckoutPaymentSucceeded constructor.
     *
     * @param  Order  $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }
}
