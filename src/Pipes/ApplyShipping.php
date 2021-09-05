<?php

namespace FiftySq\Commerce\Pipes;

use Closure;
use FiftySq\Commerce\Contracts\CartPipe;
use FiftySq\Commerce\Data\Models\PendingOrder;

class ApplyShipping implements CartPipe
{
    public function handle(PendingOrder $pendingOrder, Closure $next)
    {
        $pendingOrder->setShippingTotal(0);

        return $next($pendingOrder);
    }
}
