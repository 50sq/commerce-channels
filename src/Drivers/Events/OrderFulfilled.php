<?php

namespace FiftySq\Commerce\Channels\Drivers\Events;

use FiftySq\Commerce\Channels\Drivers\Data\Models\Order;
use Illuminate\Queue\SerializesModels;

class OrderFulfilled
{
    use SerializesModels;

    public Order $order;

    /**
     * OrderFulfilled constructor.
     *
     * @param  Order  $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }
}
