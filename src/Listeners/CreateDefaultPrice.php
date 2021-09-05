<?php

namespace FiftySq\Commerce\Listeners;

use FiftySq\Commerce\Events\VariantCreated;

class CreateDefaultPrice
{
    public function handle(VariantCreated $event)
    {
        $event->variant->prices()->create([
            'currency' => config('commerce.currency'),
            'unit_amount' => $event->variant->unit_price_cents,
        ]);

        // TODO: Register variant and price with gateway
    }
}
