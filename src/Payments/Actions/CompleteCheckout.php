<?php

namespace FiftySq\Commerce\Payments\Actions;

use FiftySq\Commerce\Data\Models\Order;
use FiftySq\Commerce\Data\Models\PendingOrder;
use FiftySq\Commerce\Data\Payment;

class CompleteCheckout
{
    /**
     * @param  PendingOrder  $pendingOrder
     * @param  Payment  $data
     * @return Order
     */
    public function __invoke(PendingOrder $pendingOrder, Payment $data): Order
    {
        $order = $pendingOrder->customer->orders()->create([
            'gateway' => $pendingOrder->gateway,
            'gateway_checkout_id' => $data->getGatewayCheckoutId(),
            'payment_status' => $data->getPaymentStatus(),
        ]);

        $pendingOrder->order()->associate($order);
        $pendingOrder->save();

        $order->items()->saveMany($pendingOrder->items);

        $order->billingAddress()->associate($pendingOrder->billingAddress);
        $order->shippingAddress()->associate($pendingOrder->shippingAddress);
        $order->save();

        $order->payments()->create([
            'payment_cents' => $data->getAmountCents(),
            'status' => $data->getPaymentStatus(),
            'completed_at' => now(),
        ]);

        return $order;
    }
}
