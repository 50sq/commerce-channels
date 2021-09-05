<?php

namespace FiftySq\Commerce\Pipes;

use Closure;
use FiftySq\Commerce\Contracts\CartPipe;
use FiftySq\Commerce\Data\Models\PendingOrder;

class RemoveZeroedItems implements CartPipe
{
    /**
     * Remove any item that has a quantity less than or equal to zero.
     *
     * @param PendingOrder $pendingOrder
     * @param Closure $next
     * @return mixed|void
     */
    public function handle(PendingOrder $pendingOrder, Closure $next)
    {
        $pendingOrder->items->filter(fn ($item) => $item->quantity <= 0)->each->delete();

        return $next($pendingOrder);
    }
}
