<?php

namespace FiftySq\Commerce\Pipes;

use Closure;
use FiftySq\Commerce\Contracts\CartPipe;
use FiftySq\Commerce\Data\Models\PendingOrder;
use FiftySq\Commerce\Features;

class ApplyDiscounts implements CartPipe
{
    public function handle(PendingOrder $pendingOrder, Closure $next)
    {
        if (Features::enabled('discounts')) {
            $subtotal = $pendingOrder->getSubtotal();

            $discounts = [];

            foreach ($pendingOrder->getDiscounts() as $discount) {
                if ($discount->type === 'fixed') {
                    $discounts[] = $discount->amount;
                } elseif ($discount->type === 'percentage') {
                    $discounts[] = $subtotal * ($discount->amount / 100);
                }
            }

            if (count($discounts) > 0) {
                rsort($discounts, SORT_NUMERIC);
                $pendingOrder->setDiscountTotal($discounts[0]);
            }
        }

        return $next($pendingOrder);
    }
}
