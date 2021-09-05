<?php

namespace FiftySq\Commerce\Pipes;

use Closure;
use FiftySq\Commerce\Contracts\CartPipe;
use FiftySq\Commerce\Data\Models\PendingOrder;

class ApplyTaxes implements CartPipe
{
    public function handle(PendingOrder $pendingOrder, Closure $next)
    {
        // Apply the taxes to the subtotal AFTER discounts
        // Include shipping total with tax calcs depending on location
        // https://www.avalara.com/blog/en/north-america/2018/11/how-to-handle-sales-tax-on-shipping-a-state-by-state-guide.html

        $pendingOrder->setSalesTaxTotal(0);

        return $next($pendingOrder);
    }
}
