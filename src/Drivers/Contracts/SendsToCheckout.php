<?php

namespace FiftySq\Commerce\Channels\Drivers\Contracts;

use FiftySq\Commerce\Channels\Drivers\Data\Models\PendingOrder;

interface SendsToCheckout
{
    /**
     * Send the customer to the channel cart for checkout.
     *
     * @return mixed
     */
    public function sendToCheckout(PendingOrder $pending_order);
}
