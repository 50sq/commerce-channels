<?php

namespace FiftySq\Commerce\Channels\Webhooks;

use FiftySq\Commerce\Channels\Events\OrderFulfilled;
use FiftySq\Commerce\Data\Models\Order;
use Illuminate\Database\Eloquent\Model;

class OrderUpdated
{
    protected string $type;
    protected string $channel;
    protected string $channelOrderId;
    protected string $orderStatus;

    /**
     * OrderUpdated constructor.
     *
     * @param  string  $type
     * @param  string  $channel
     * @param  string  $channelOrderId
     * @param  string  $orderStatus
     */
    public function __construct(string $type, string $channel, string $channelOrderId, string $orderStatus)
    {
        $this->type = $type;
        $this->channel = $channel;
        $this->channelOrderId = $channelOrderId;
        $this->orderStatus = $orderStatus;
    }

    public function handle()
    {
        $order = $this->findOrder();

        $order->order_status = $this->orderStatus;
        $order->save();

        switch ($this->orderStatus) {
            case Order::STATUS_FULFILLED:
                event(new OrderFulfilled($order));

                return;
        }
    }

    /**
     * @return Model|Order
     */
    protected function findOrder(): Order
    {
        return Order::query()
            ->where('channel', $this->channel)
            ->where('channel_order_id', $this->channelOrderId)->first();
    }
}
