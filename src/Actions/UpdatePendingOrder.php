<?php

namespace FiftySq\Commerce\Actions;

use FiftySq\Commerce\Commerce;
use FiftySq\Commerce\Data\Models\Discount;
use FiftySq\Commerce\Data\Models\PendingOrder;
use FiftySq\Commerce\Http\Requests\UpdatePendingOrderRequest;

class UpdatePendingOrder
{
    /**
     * @return mixed
     */
    public function __invoke(UpdatePendingOrderRequest $request, PendingOrder $pendingOrder)
    {
        foreach ($request->input('line_items', []) as $data) {
            $lineItem = $pendingOrder->items()->where(Commerce::prefix('variant_id'), $data['id']);

            if ($data['quantity'] === 0) {
                $lineItem->delete();
            } else {
                $lineItem->update(['quantity' => $data['quantity']]);
            }
        }

        if ($request->filled('coupon_code')) {
            $pendingOrder->discounts()->attach(Discount::firstWhere('code', $request->input('coupon_code')));
        }

        if ($request->filled('billing_address_id')) {
            $pendingOrder->commerce_billing_address_id = $request->input('billing_address_id');
        }

        if ($request->filled('shipping_address_id')) {
            $pendingOrder->commerce_shipping_address_id = $request->input('shipping_address_id');
        }

        if ($pendingOrder->isDirty()) {
            $pendingOrder->save();
        }
    }
}
