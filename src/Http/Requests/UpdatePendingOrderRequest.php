<?php

namespace FiftySq\Commerce\Http\Requests;

use FiftySq\Commerce\Commerce;
use FiftySq\Commerce\Data\Models\Address;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePendingOrderRequest extends FormRequest
{
    use InteractsWithPendingOrder;

    public function rules()
    {
        return [
            'line_items' => ['array'],
            'line_items.*.id' => [
                'integer',
                Rule::exists(Commerce::prefix('line_items'), Commerce::prefix('variant_id'))
                    ->where(Commerce::prefix('cart_id'), $this->pendingOrder()->id),
            ],
            'line_items.*.quantity' => ['integer', 'min:0'],

            'coupon_code' => ['max:25', Rule::exists(Commerce::prefix('discounts'), 'code')],

            'billing_address_id' => [Rule::exists(Commerce::prefix('addresses'), 'id')->where(function ($query) {
                $query->where('type', Address::TYPE_BILLING);
                $query->where(Commerce::prefix('customer_id'), $this->customer()->id);
            })],
            'shipping_address_id' => [Rule::exists(Commerce::prefix('addresses'), 'id')->where(function ($query) {
                $query->where('type', Address::TYPE_SHIPPING);
                $query->where(Commerce::prefix('customer_id'), $this->customer()->id);
            })],
        ];
    }
}
