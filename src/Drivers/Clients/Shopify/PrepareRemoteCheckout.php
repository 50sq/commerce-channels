<?php

namespace FiftySq\Commerce\Channels\Drivers\Clients\Shopify;

use FiftySq\Commerce\Channels\Drivers\Data\Models\PendingOrder;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;

class PrepareRemoteCheckout implements Arrayable
{
    protected PendingOrder $pending_order;

    /**
     * PrepareRemoteCheckout constructor.
     *
     * @param  PendingOrder  $pending_order
     */
    public function __construct(PendingOrder $pending_order)
    {
        $this->pending_order = $pending_order;
    }

    /**
     * @return array[]
     */
    public function toArray(): array
    {
        return [
            'checkout' => [
                'billing_address' => $this->pending_order->getBillingAddress(),
                'customer_id' => $this->pending_order->getCustomerId(),
                'discount_code' => '',
                'email' => $this->pending_order->getContactEmail(),
                'line_items' => $this->mapLineItems(),
                'phone' => $this->pending_order->getContactPhone(),
                'presentment_currency' => $this->pending_order->getCustomerCurrency(),
                'shipping_address' => $this->pending_order->getShippingAddress(),
                'shipping_line' => [],
                'source_name' => Str::snake(config('app.name')),
            ],
        ];
    }

    /**
     * Return Shopify-specific line items.
     *
     * @return array
     */
    protected function mapLineItems(): array
    {
        return $this->pending_order->items()->whereHas('product', fn ($query) => $query->where('channel', 'shopify'))
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->variant->commerce_product_id,
                    'quantity' => $item->quantity,
                    'requires_shipping' => true,
                    'sku' => $item->variant->sku,
                    'taxable' => $item->variant->taxable,
                    'variant_id' => $item->variant->external_id,
                ];
            })
            ->values()->toArray();
    }
}
