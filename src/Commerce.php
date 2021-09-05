<?php

namespace FiftySq\Commerce;

use FiftySq\Commerce\Contracts\CheckoutCancellationViewResponse;
use FiftySq\Commerce\Contracts\CheckoutSuccessViewResponse;
use FiftySq\Commerce\Contracts\CheckoutViewResponse;
use FiftySq\Commerce\Contracts\OrderIdGeneratorContract;
use FiftySq\Commerce\Contracts\SkuGeneratorContract;
use FiftySq\Commerce\Data\Models\Customer;
use FiftySq\Commerce\Data\Models\Model;
use FiftySq\Commerce\Http\Middleware\ResolveCustomer;
use FiftySq\Commerce\Http\Responses\SimpleViewResponse;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class Commerce
{
    /**
     * Indicates if Laravel Commerce routes will be registered.
     *
     * @var bool
     */
    public static $registersRoutes = true;

    /**
     * The sellable model that should be used by Commerce.
     *
     * @var string
     */
    public static $sellableModel = 'App\\Models\\Product';

    /**
     * The sellable resource that should be used by Commerce.
     *
     * @var string
     */
    public static $sellableResource = 'App\\Http\\Resources\\ProductResource';

    /**
     * Prefix a string within the Commerce namespace.
     *
     * @param $string
     * @return string
     */
    public static function prefix($string): string
    {
        return implode('_', [config('commerce.table_prefix', 'commerce'), $string]);
    }

    /**
     * Get the name of the sellable model used by the application.
     *
     * @return string
     */
    public static function sellableModel(): string
    {
        return static::$sellableModel;
    }

    /**
     * Get a sellable configuration builder for the given class.
     */
    public static function sellable()
    {
        $model = static::sellableModel();

        return new $model;
    }

    /**
     * Specify the sellable model that should be used by Commerce.
     *
     * @param  string  $model
     * @return static
     */
    public static function useSellableModel(string $model): self
    {
        static::$sellableModel = $model;

        return new static;
    }

    /**
     * Specify the sellable resource that should be used by Commerce.
     *
     * @param  string  $resource
     * @return static
     */
    public static function renderProductsWith(string $resource): self
    {
        static::$sellableResource = $resource;

        return new static;
    }

    /**
     * Specify the sellable resource that should be used by Commerce.
     *
     * @param $items
     * @return AnonymousResourceCollection
     */
    public static function toCollection($items): AnonymousResourceCollection
    {
        $resource = new static::$sellableResource([]);

        return $resource::collection($items);
    }

    /**
     * Specify which view should be used as the checkout cancelled view.
     *
     * @param  callable|string  $view
     * @return void
     */
    public static function checkoutCancelledView($view)
    {
        app()->singleton(CheckoutCancellationViewResponse::class, function () use ($view) {
            return new SimpleViewResponse($view);
        });
    }

    /**
     * Specify which view should be used as the checkout preview view.
     *
     * @param  callable|string  $view
     * @return void
     */
    public static function checkoutView($view)
    {
        app()->singleton(CheckoutViewResponse::class, function () use ($view) {
            return new SimpleViewResponse($view);
        });
    }

    /**
     * Specify which view should be used as the checkout success view.
     *
     * @param  callable|string  $view
     * @return void
     */
    public static function checkoutSuccessView($view)
    {
        app()->singleton(CheckoutSuccessViewResponse::class, function () use ($view) {
            return new SimpleViewResponse($view);
        });
    }

    /**
     * Register a class / callback that should be used to generate SKUs.
     *
     * @param string $class
     * @return void
     */
    public static function generateSkusUsing(string $class)
    {
        app()->singleton(SkuGeneratorContract::class, $class);
    }

    /**
     * Register a class / callback that should be used to generate order IDs.
     *
     * @param string $class
     * @return void
     */
    public static function generateOrderIds(string $class)
    {
        app()->singleton(OrderIdGeneratorContract::class, $class);
    }

    /**
     * Configure Laravel Commerce to not register its routes.
     *
     * @return static
     */
    public static function ignoreRoutes()
    {
        static::$registersRoutes = false;

        return new static;
    }

    /**
     * Return middleware.
     *
     * @return Repository|Application|ResolveCustomer[]
     */
    public static function middleware()
    {
        return array_merge(
            config('commerce.middleware', ['web']),
            [ResolveCustomer::class]
        );
    }

    /**
     * Return the customer identifier for the session.
     *
     * @return string
     */
    public static function customerSessionKey(): string
    {
        return (new Customer())->getForeignKey();
    }

    /**
     * Return the customer value used for the session.
     *
     * @return string
     */
    public static function customerSessionValue(): string
    {
        return 'uuid';
    }
}
