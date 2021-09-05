<?php

namespace FiftySq\Commerce\Generators;

use FiftySq\Commerce\Data\Models\Cart;
use FiftySq\Commerce\Data\Models\Customer;
use Illuminate\Support\Carbon;

class OrderIdGenerator
{
    private static string $regex = '/[^A-Za-z0-9 ]/';

    /**
     * Generate an order ID.
     *
     * @param Customer $customer
     * @param Cart $cart
     * @return string
     */
    public static function generate(Customer $customer, Cart $cart): string
    {
        return implode('-', [
            substr((string) Carbon::now()->year, 1),
            substr(preg_replace(self::$regex, '', (string) $customer->uuid), 0, 6),
            substr(preg_replace(self::$regex, '', (string) $cart->uuid), 0, 6),
        ]);
    }
}
