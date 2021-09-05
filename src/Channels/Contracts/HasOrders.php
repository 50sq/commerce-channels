<?php

namespace FiftySq\Commerce\Channels\Contracts;

use FiftySq\Commerce\Data\Order;
use FiftySq\Commerce\Data\ShippingAddress;
use Illuminate\Http\Request;

interface HasOrders
{
    /**
     * @param $orderId
     * @return mixed
     */
    public function getOrder($orderId);

    /**
     * @param  Order  $order
     * @param  ShippingAddress|null  $address
     * @param  string|null  $shippingMethod
     * @return mixed
     */
    public function estimateCost(Order $order, ?ShippingAddress $address, string $shippingMethod = null);

    /**
     * @param  Order  $order
     * @param  bool  $confirmed
     * @param  ShippingAddress|null  $address
     * @param  string|null  $shippingMethod
     * @return mixed
     */
    public function createOrder(Order $order, bool $confirmed, ?ShippingAddress $address, string $shippingMethod = null);

    /**
     * @param  Request  $request
     * @return mixed
     */
    public function processWebhook(Request $request);
}
