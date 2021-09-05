<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use FiftySq\Commerce\Generators\OrderIdGenerator;
use FiftySq\Commerce\Generators\SkuGenerator;
use FiftySq\Commerce\Commerce;
use Tests\Fixtures\ProductResource;

class CommerceServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Commerce::generateSkusUsing(SkuGenerator::class);
        Commerce::generateOrderIds(OrderIdGenerator::class);
        Commerce::renderProductsWith(ProductResource::class);
    }
}
