<?php

namespace FiftySq\Commerce\Data\Models;

use FiftySq\Commerce\Data\Address as AddressObject;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Address extends Model
{
    use SoftDeletes;

    public const TYPE_BILLING = 'billing';
    public const TYPE_SHIPPING = 'shipping';

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, $this->prefix('customer_id'));
    }

    public function toDto(): AddressObject
    {
        $classname = '\\FiftySq\\Commerce\\Data\\'.Str::studly($this->type.'_address');

        $class = new $classname();

        $class->setAddress1($this->address1);
        $class->setAddress2($this->address2);
        $class->setCity($this->city);
        $class->setRegion($this->region);
        $class->setCountry($this->country);
        $class->setPostalCode($this->postal_code);

        return $class;
    }
}
