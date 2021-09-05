<?php

namespace FiftySq\Commerce\Actions;

use FiftySq\Commerce\Data\Models\PendingOrder;

class CompleteCheckout
{
    /**
     * @return mixed
     */
    public function __invoke(PendingOrder $pending_order)
    {
        $pending_order->prepareForCheckout();
    }
}
