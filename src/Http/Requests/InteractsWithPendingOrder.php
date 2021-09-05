<?php

namespace FiftySq\Commerce\Http\Requests;

use FiftySq\Commerce\Data\Models\PendingOrder;

trait InteractsWithPendingOrder
{
    /**
     * @var PendingOrder|null
     */
    protected ?PendingOrder $pendingOrder;

    /**
     * InteractsWithPendingOrder constructor.
     */
    public function __construct()
    {
        $this->pendingOrder = null;
    }

    /**
     * @return PendingOrder
     */
    public function pendingOrder(): PendingOrder
    {
        if (! $this->pendingOrder) {
            $this->pendingOrder = $this->customer()->pendingOrder;
        }

        return $this->pendingOrder;
    }
}
