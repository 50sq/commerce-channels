<?php

namespace FiftySq\Commerce\Actions;

use FiftySq\Commerce\Data\Models\Address;
use FiftySq\Commerce\Data\Models\Customer;
use FiftySq\Commerce\Http\Requests\CreateAddressRequest;

class CreateAddress
{
    /**
     * @param Customer $customer
     * @param CreateAddressRequest $request
     * @return mixed
     */
    public function __invoke(Customer $customer, CreateAddressRequest $request)
    {
        if ($request->boolean('is_default') === true) {
            $customer->addresses()->where('type', $request->input('type'))->update([
                'is_default' => false,
            ]);
        }

        if ($request->input('type') === Address::TYPE_SHIPPING && $request->boolean('same_as_billing')) {
            $data = $customer->addresses()->firstWhere('type', Address::TYPE_BILLING)->only([
                'address1', 'address2', 'city',
                'region', 'postal_code', 'country',
            ]);

            $data['type'] = $request->input('type');
        } else {
            $data = $request->only([
                'type', 'is_default', 'address1', 'address2',
                'city', 'region', 'postal_code', 'country',
            ]);
        }

        $address = $customer->addresses()->create($data);

        if ($customer->defaultAddress($address->type)->doesntExist()) {
            $address->is_default = true;
            $address->save();
        }
    }
}
