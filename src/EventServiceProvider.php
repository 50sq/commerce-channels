<?php

namespace FiftySq\Commerce\Channels;

use FiftySq\Commerce\Events\VariantCreated;
use FiftySq\Commerce\Listeners\CreateDefaultPrice;
use FiftySq\Commerce\Listeners\InitiateFulfillment;
use FiftySq\Commerce\Payments\Events\CheckoutPaymentSucceeded;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        CheckoutPaymentSucceeded::class => [
            InitiateFulfillment::class,
        ],
        VariantCreated::class => [
            CreateDefaultPrice::class,
        ],
    ];
}
