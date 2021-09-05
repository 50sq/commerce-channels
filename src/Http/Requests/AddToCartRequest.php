<?php

namespace FiftySq\Commerce\Http\Requests;

use FiftySq\Commerce\Commerce;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddToCartRequest extends FormRequest
{
    public function rules()
    {
        return [
            'id' => ['required', Rule::exists(Commerce::prefix('variants'), 'id')],
            'quantity' => ['required', 'integer', 'min:0'],
            'options' => ['array'],
        ];
    }
}
