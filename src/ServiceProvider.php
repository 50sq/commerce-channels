<?php

namespace FiftySq\Commerce\Channels;

use FiftySq\Commerce\Channels\Drivers\Clients\Shopify\AdminClient;
use FiftySq\Commerce\Channels\Drivers\Clients\Shopify\StorefrontClient;
use FiftySq\Commerce\Channels\Drivers\Contracts\HasDiscounts;
use FiftySq\Commerce\Channels\Drivers\Contracts\HasOrders;
use FiftySq\Commerce\Channels\Drivers\Contracts\HasShippingRates;
use FiftySq\Commerce\Channels\Drivers\Contracts\PullsProducts;
use FiftySq\Commerce\Channels\Drivers\Contracts\PushesProducts;
use FiftySq\Commerce\Channels\Drivers\Contracts\SendsToCheckout;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Printful\PrintfulApiClient;

class ServiceProvider extends BaseServiceProvider implements DeferrableProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/channels.php', 'commerce.channels');
        $this->registerManagers();
        $this->registerChannels();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->configurePublishing();
    }

    /**
     * Publish Config.
     *
     * @return void
     */
    public function configurePublishing()
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__.'/../config/channels.php' => config_path('commerce/channels.php'),
        ], 'commerce-channels-config');

        $this->publishes([
            __DIR__.'/../database/migrations/2021_04_19_000003_create_commerce_discounts_table.php' => database_path('migrations'),
        ], 'commerce-migrations-discounts');
    }

    /**
     * Register managers.
     */
    protected function registerManagers()
    {
        $this->app->singleton(ChannelManager::class, function ($app) {
            return new ChannelManager($app);
        });

        $this->app->alias(ChannelManager::class, HasDiscounts::class);
        $this->app->alias(ChannelManager::class, HasOrders::class);
        $this->app->alias(ChannelManager::class, HasShippingRates::class);
        $this->app->alias(ChannelManager::class, PullsProducts::class);
        $this->app->alias(ChannelManager::class, PushesProducts::class);
        $this->app->alias(ChannelManager::class, SendsToCheckout::class);
    }

    /**
     * Register channels.
     */
    protected function registerChannels()
    {
        if ($key = config('commerce.channels.drivers.printful.key')) {
            $this->app->singleton(
                PrintfulApiClient::class,
                fn () => new PrintfulApiClient($key)
            );
        }

        if ($key = config('commerce.channels.drivers.shopify.admin_key')) {
            $this->app->singleton(
                AdminClient::class,
                fn () => new AdminClient(
                    config('commerce.channels.drivers.shopify.shop_name'),
                    $key,
                    config('commerce.channels.drivers.shopify.admin_password'),
                )
            );

            $this->app->singleton(
                StorefrontClient::class,
                fn () => new StorefrontClient(
                    config('commerce.channels.drivers.shopify.shop_name'),
                    config('commerce.channels.drivers.shopify.storefront_token'),
                )
            );
        }
    }

    public function provides()
    {
        return ['commerce.channels'];
    }
}
