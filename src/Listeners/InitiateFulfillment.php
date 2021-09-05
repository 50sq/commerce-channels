<?php

namespace FiftySq\Commerce\Listeners;

use FiftySq\Commerce\Channels\ChannelManager;
use FiftySq\Commerce\Data\Models\Order;
use FiftySq\Commerce\Data\Order as OrderObject;
use FiftySq\Commerce\Payments\Events\CheckoutPaymentSucceeded;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class InitiateFulfillment implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    public ChannelManager $manager;

    /**
     * InitiateFulfillment constructor.
     *
     * @param  ChannelManager  $manager
     */
    public function __construct(ChannelManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param  CheckoutPaymentSucceeded  $event
     * @return Order
     */
    public function handle(CheckoutPaymentSucceeded $event): Order
    {
        $data = $this->buildOrderObject($event->order);

        $orderObject = $this->manager->createOrder($data, true, $data->getShippingAddress());

        $order = Order::find($data->getId());

        $order->update([
            'channel' => $orderObject->getChannel(),
            'channel_order_id' => $orderObject->getExternalOrderId(),
            'order_status' => $orderObject->getStatus(),
        ]);

        return $order;
    }

    /**
     * @param  Order  $order
     * @return OrderObject
     */
    protected function buildOrderObject(Order $order): OrderObject
    {
        $object = new OrderObject($this->manager->getDriver(), $order->id, $order->items->toArray());

        $object->setShippingAddress($order->shippingAddress->toDto());

        return $object;
    }
}
