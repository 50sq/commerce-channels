<?php

namespace FiftySq\Commerce\Channels;

use FiftySq\Commerce\Channels\Drivers\Contracts\HasOrders;
use FiftySq\Commerce\Channels\Drivers\Contracts\HasShippingRates;
use FiftySq\Commerce\Channels\Drivers\Contracts\PullsProducts;
use FiftySq\Commerce\Channels\Drivers\Contracts\PushesProducts;
use FiftySq\Commerce\Channels\Drivers\Contracts\SendsToCheckout;
use FiftySq\Commerce\Channels\Drivers\Data\Models\PendingOrder;
use FiftySq\Commerce\Channels\Drivers\Data\Order;
use FiftySq\Commerce\Channels\Drivers\Data\ShippingAddress;
use FiftySq\Commerce\Channels\Drivers\DriverManager;
use Illuminate\Http\Request;
use Illuminate\Support\Manager;
use InvalidArgumentException;

class ChannelManager extends Manager implements
    HasOrders,
    HasShippingRates,
    PullsProducts,
    PushesProducts,
    SendsToCheckout
{
    /**
     * The default channel.
     *
     * @var string
     */
    protected $defaultChannel = 'null';

    /**
     * Create Amazon driver.
     *
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function createAmazonDriver()
    {
        return $this->container->make(Drivers\AmazonChannel::class);
    }

    /**
     * Create Big Cartel driver.
     *
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function createBigcartelDriver()
    {
        return $this->container->make(Drivers\BigCartelChannel::class);
    }

    /**
     * Create Printful driver.
     *
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function createPrintfulDriver()
    {
        return $this->container->make(Drivers\PrintfulChannel::class);
    }

    /**
     * Create Shopify driver.
     *
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function createShopifyDriver()
    {
        return $this->container->make(Drivers\ShopifyChannel::class);
    }

    /**
     * Get a channel instance.
     *
     * @param  string|null  $name
     * @return mixed
     */
    public function channel(string $name = null)
    {
        return $this->driver($name);
    }

    /**
     * Create a new driver instance.
     *
     * @param  string  $driver
     * @return mixed
     *
     * @throws \InvalidArgumentException|\Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function createDriver($driver)
    {
        try {
            return parent::createDriver($driver);
        } catch (InvalidArgumentException $e) {
            if (class_exists($driver)) {
                return $this->container->make($driver);
            }

            throw $e;
        }
    }

    public function getDefaultDriver()
    {
        return $this->defaultChannel;
    }

    /**
     * @return string
     */
    public function getDriver(): string
    {
        return (new DriverManager($this))->driver();
    }

    /**
     * Return products.
     *
     * @param array $filters
     * @return mixed
     */
    public function getProducts(array $filters = [])
    {
        return (new DriverManager($this))->getProducts($filters);
    }

    /**
     * Get products.
     *
     * @param $productId
     * @return mixed
     */
    public function getProduct($productId)
    {
        return (new DriverManager($this))->getProduct($productId);
    }

    /**
     * Get variant.
     *
     * @param $variantId
     * @return mixed
     */
    public function getVariant($variantId)
    {
        return (new DriverManager($this))->getVariant($variantId);
    }

    /**
     * Update variant.
     *
     * @param $variantId
     * @param array $values
     * @return mixed
     */
    public function updateVariant($variantId, array $values)
    {
        return (new DriverManager($this))->updateVariant($variantId, $values);
    }

    /**
     * Delete product.
     *
     * @param $productId
     * @return mixed
     */
    public function deleteProduct($productId)
    {
        return (new DriverManager($this))->deleteProduct($productId);
    }

    /**
     * Delete variant.
     *
     * @param $variantId
     * @return mixed
     */
    public function deleteVariant($variantId)
    {
        return (new DriverManager($this))->deleteVariant($variantId);
    }

    public function createProduct(array $values)
    {
        // TODO: Implement createProduct() method.
    }

    public function updateProduct($productId, array $values)
    {
        // TODO: Implement updateProduct() method.
    }

    public function appendProductImages($productId, array $values)
    {
        // TODO: Implement appendProductImages() method.
    }

    public function removeProductImages($productId, array $values)
    {
        // TODO: Implement removeProductImages() method.
    }

    public function createVariant($productId, array $values)
    {
        // TODO: Implement createVariant() method.
    }

    public function appendVariantImages($productId, array $values)
    {
        // TODO: Implement appendVariantImages() method.
    }

    public function removeVariantImages($productId, array $values)
    {
        // TODO: Implement removeVariantImages() method.
    }

    public function sendToCheckout(PendingOrder $pending_order)
    {
        // TODO: Implement sendToCheckout() method.
    }

    public function getOrder($orderId)
    {
        // TODO: Implement getOrder() method.
    }

    public function estimateCost(Order $order, ?ShippingAddress $address, string $shippingMethod = null)
    {
        // TODO: Implement estimateCost() method.
    }

    /**
     * @param  Order  $order
     * @param  bool  $confirmed
     * @param  ShippingAddress|null  $address
     * @param  string|null  $shippingMethod
     * @return Order
     */
    public function createOrder(Order $order, bool $confirmed, ?ShippingAddress $address, string $shippingMethod = null): Order
    {
        return (new DriverManager($this))->createorder($order, $confirmed, $address, $shippingMethod);
    }

    public function calculateShippingRates(ShippingAddress $address, array $items): array
    {
        // TODO: Implement calculateShippingRates() method.
    }

    /**
     * @param  Request  $request
     * @return mixed
     */
    public function processWebhook(Request $request)
    {
        return (new DriverManager($this))->processWebhook($request);
    }
}
