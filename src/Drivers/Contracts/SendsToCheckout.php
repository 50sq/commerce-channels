<?php

namespace FiftySq\Commerce\Channels\Drivers\Contracts;

interface SendsToCheckout
{
    /**
     * Send the customer to the channel cart for checkout.
     *
     * @return mixed
     */
    public function sendToCheckout($pending_order);
}
