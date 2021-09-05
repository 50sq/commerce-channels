<?php

namespace FiftySq\Commerce\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RemoveFromCartRequest extends FormRequest
{
    public function rules()
    {
        return [
            'id' => ['required', Rule::exists('commerce_variants', 'id')],
            'quantity' => ['required', 'integer', 'min:0'],
        ];
    }
}
