<?php

namespace FiftySq\Commerce\Pipes;

use Closure;
use FiftySq\Commerce\Contracts\CartPipe;
use FiftySq\Commerce\Data\Models\PendingOrder;

class SubtotalItems implements CartPipe
{
    /**
     * Subtotal the.
     *
     * @param PendingOrder $pendingOrder
     * @param Closure $next
     * @return mixed|void
     */
    public function handle(PendingOrder $pendingOrder, Closure $next)
    {
        $subtotal = $pendingOrder->items->reduce(function ($total, $item) {
            return $total + ($item->unit_price * $item->quantity);
        }, 0);

        $pendingOrder->setSubtotal($subtotal);

        return $next($pendingOrder);
    }
}
