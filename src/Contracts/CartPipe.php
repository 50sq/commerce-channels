<?php

namespace FiftySq\Commerce\Contracts;

use Closure;
use FiftySq\Commerce\Data\Models\PendingOrder;

interface CartPipe
{
    /**
     * @param PendingOrder $pendingOrder
     * @param Closure $next
     * @return mixed
     */
    public function handle(PendingOrder $pendingOrder, Closure $next);
}
