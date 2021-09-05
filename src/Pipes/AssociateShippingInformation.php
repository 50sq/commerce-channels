<?php

namespace FiftySq\Commerce\Pipes;

use Closure;
use FiftySq\Commerce\Contracts\CartPipe;
use FiftySq\Commerce\Data\Models\PendingOrder;

class AssociateShippingInformation implements CartPipe
{
    public function handle(PendingOrder $pendingOrder, Closure $next)
    {
        return $next($pendingOrder);
    }
}
