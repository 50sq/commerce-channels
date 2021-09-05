<?php

namespace FiftySq\Commerce\Channels\Contracts;

use FiftySq\Commerce\Data\ShippingAddress;

interface HasShippingRates
{
    /**
     * @param  ShippingAddress  $address
     * @param  array  $items
     * @return array
     */
    public function calculateShippingRates(ShippingAddress $address, array $items): array;
}
