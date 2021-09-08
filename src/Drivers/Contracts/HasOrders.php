<?php

namespace FiftySq\Commerce\Channels\Drivers\Contracts;

use Illuminate\Http\Request;

interface HasOrders
{
    /**
     * @param $orderId
     * @return mixed
     */
    public function getOrder($orderId);

    /**
     * @param $order
     * @param  null  $address
     * @param  string|null  $shippingMethod
     * @return mixed
     */
    public function estimateCost($order, $address = null, string $shippingMethod = null);

    /**
     * @param $order
     * @param  bool  $confirmed
     * @param  null  $address
     * @param  string|null  $shippingMethod
     * @return mixed
     */
    public function createOrder($order, bool $confirmed, $address = null, string $shippingMethod = null);

    /**
     * @param  Request  $request
     * @return mixed
     */
    public function processWebhook(Request $request);
}
