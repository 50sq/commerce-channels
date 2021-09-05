<?php

namespace FiftySq\Commerce\Channels\Contracts;

use FiftySq\Commerce\Data\Models\PendingOrder;

interface SendsToCheckout
{
    /**
     * Send the customer to the channel cart for checkout.
     *
     * @return mixed
     */
    public function sendToCheckout(PendingOrder $pending_order);
}
