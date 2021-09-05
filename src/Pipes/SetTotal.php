<?php

namespace FiftySq\Commerce\Pipes;

use Closure;
use FiftySq\Commerce\Contracts\CartPipe;
use FiftySq\Commerce\Data\Models\PendingOrder;

class SetTotal implements CartPipe
{
    public function handle(PendingOrder $pendingOrder, Closure $next)
    {
        $pendingOrder->setTotal($pendingOrder->getSubtotal() + $pendingOrder->getSalesTaxTotal());

        return $next($pendingOrder);
    }
}
