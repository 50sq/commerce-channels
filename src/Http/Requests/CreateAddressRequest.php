<?php

namespace FiftySq\Commerce\Http\Requests;

use FiftySq\Commerce\Data\Models\Address;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateAddressRequest extends FormRequest
{
    public function rules()
    {
        return [
            'type' => ['required', Rule::in([Address::TYPE_BILLING, Address::TYPE_SHIPPING])],
            'is_default' => ['boolean'],
            'address1' => [Rule::requiredIf($this->isRequired()), 'max:250'],
            'address2' => ['max:250'],
            'city' => [Rule::requiredIf($this->isRequired()), 'max:250'],
            'region' => [Rule::requiredIf($this->isRequired()), 'max:250'],
            'postal_code' => [Rule::requiredIf($this->isRequired()), 'max:20'],
            'country' => [Rule::requiredIf($this->isRequired()), 'max:2'],
            'same_as_billing' => [
                'boolean',
                Rule::requiredIf(fn () => Address::where('user_id', $this->customer()->id)->where('type', Address::TYPE_BILLING)->exists()),
            ],
        ];
    }

    /**
     * @return bool
     */
    protected function isRequired(): bool
    {
        return $this->input('type') === Address::TYPE_BILLING || ! $this->boolean('same_as_billing');
    }
}
