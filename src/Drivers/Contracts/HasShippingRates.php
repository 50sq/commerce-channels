<?php

namespace FiftySq\Commerce\Channels\Drivers\Contracts;

use FiftySq\Commerce\Channels\Drivers\Data\ShippingAddress;

interface HasShippingRates
{
    /**
     * @param  ShippingAddress  $address
     * @param  array  $items
     * @return array
     */
    public function calculateShippingRates(ShippingAddress $address, array $items): array;
}
