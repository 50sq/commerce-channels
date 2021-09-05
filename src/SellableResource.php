<?php

namespace FiftySq\Commerce;

trait SellableResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->product->title,
            'quantity' => $this->quantity,
            'unit_price_cents' => $this->unit_price_cents,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
