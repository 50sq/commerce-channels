<?php

namespace FiftySq\Commerce\Channels;

use FiftySq\Commerce\Channels\Contracts\HasOrders;
use FiftySq\Commerce\Channels\Contracts\HasShippingRates;
use FiftySq\Commerce\Channels\Contracts\PullsProducts;
use FiftySq\Commerce\Channels\Contracts\PushesProducts;
use FiftySq\Commerce\Channels\Webhooks\OrderUpdated;
use FiftySq\Commerce\Data\Models\Order;
use FiftySq\Commerce\Data\Order as OrderObject;
use FiftySq\Commerce\Data\ShippingAddress;
use FiftySq\Commerce\Data\ShippingRate;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Printful\PrintfulApiClient;
use Printful\PrintfulWebhook;

class PrintfulChannel implements HasOrders, HasShippingRates, PullsProducts, PushesProducts
{
    /**
     * @var string
     */
    public string $name = 'printful';

    /**
     * @var PrintfulApiClient
     */
    protected PrintfulApiClient $client;

    /**
     * @var array
     */
    protected array $orderStatuses = [
        'draft' => Order::STATUS_DRAFT,
        'pending' => Order::STATUS_PENDING,
        'failed' => Order::STATUS_FAILED,
        'canceled' => Order::STATUS_CANCELLED,
        'inprocess' => Order::STATUS_PROCESSING,
        'onhold' => Order::STATUS_ONHOLD,
        'partial' => Order::STATUS_PARTIAL,
        'fulfilled' => Order::STATUS_FULFILLED,
    ];

    /**
     * Channel constructor.
     *
     * @param  PrintfulApiClient  $client
     */
    public function __construct(PrintfulApiClient $client)
    {
        $this->client = $client;
    }

    /**
     * Get all syncable products and their variants.
     *
     * @param array $filters
     * @return Collection
     * @throws \Printful\Exceptions\PrintfulApiException
     * @throws \Printful\Exceptions\PrintfulException
     */
    public function getProducts(array $filters = []): Collection
    {
        $response = $this->client->get('sync/products', array_merge([
            'status' => 'all',
            'offset' => 0,
            'limit' => 100,
        ], $filters));

        return collect($response);
    }

    /**
     * Get a single product, with its information and variants.
     *
     * @param $productId
     * @return mixed
     * @throws \Printful\Exceptions\PrintfulApiException
     * @throws \Printful\Exceptions\PrintfulException
     */
    public function getProduct($productId)
    {
        return $this->client->get("sync/products/{$productId}");
    }

    /**
     * Create a new product.
     *
     * @param  array  $values
     * @return mixed
     * @throws \Printful\Exceptions\PrintfulApiException
     * @throws \Printful\Exceptions\PrintfulException
     */
    public function createProduct(array $values)
    {
        // TODO: Format payload
        $products = $values;

        return $this->client->post('sync/products', $products);
    }

    /**
     * @param $productId
     * @param  array  $values
     * @return mixed
     * @throws \Printful\Exceptions\PrintfulApiException
     * @throws \Printful\Exceptions\PrintfulException
     */
    public function updateProduct($productId, array $values)
    {
        return $this->client->put("sync/products/{$productId}", $values);
    }

    /**
     * Delete all variants of a product and return the product.
     *
     * @param $productId
     * @return mixed
     * @throws \Printful\Exceptions\PrintfulApiException
     * @throws \Printful\Exceptions\PrintfulException
     */
    public function deleteProduct($productId)
    {
        return $this->client->delete("sync/products/{$productId}");
    }

    /**
     * Get a single variant, with its information and product information.
     *
     * @param $variantId
     * @return mixed
     * @throws \Printful\Exceptions\PrintfulApiException
     * @throws \Printful\Exceptions\PrintfulException
     */
    public function getVariant($variantId)
    {
        return $this->client->get("sync/products/{$variantId}");
    }

    /**
     * @param $productId
     * @param  array  $values
     * @return mixed
     * @throws \Printful\Exceptions\PrintfulApiException
     * @throws \Printful\Exceptions\PrintfulException
     */
    public function createVariant($productId, array $values)
    {
        return $this->client->post("sync/products/{$productId}/variants", $values);
    }

    /**
     * Add or update a single variant, and return its information and product information.
     *
     * @param $variantId
     * @param array $values
     * @return mixed
     * @throws \Printful\Exceptions\PrintfulApiException
     * @throws \Printful\Exceptions\PrintfulException
     */
    public function updateVariant($variantId, array $values)
    {
        return $this->client->put("sync/variant/{$variantId}", $values);
    }

    /**
     * Delete a single variant and return the product.
     *
     * @param $variantId
     * @return mixed
     * @throws \Printful\Exceptions\PrintfulApiException
     * @throws \Printful\Exceptions\PrintfulException
     */
    public function deleteVariant($variantId)
    {
        return $this->client->delete("sync/variant/{$variantId}");
    }

    public function appendProductImages($productId, array $values)
    {
        // TODO: Implement appendProductImages() method.
    }

    public function removeProductImages($productId, array $values)
    {
        // TODO: Implement removeProductImages() method.
    }

    public function appendVariantImages($productId, array $values)
    {
        // TODO: Implement appendVariantImages() method.
    }

    public function removeVariantImages($productId, array $values)
    {
        // TODO: Implement removeVariantImages() method.
    }

    /**
     * @param $orderId
     * @return OrderObject
     * @throws \Printful\Exceptions\PrintfulApiException
     * @throws \Printful\Exceptions\PrintfulException
     */
    public function getOrder($orderId): OrderObject
    {
        $response = $this->client->get("orders/{$orderId}");

        $data = $response['result'];

        $order = new OrderObject($this->name, $data['external_id'], $data['items']);

        $order->setCosts($data['costs']);
        $order->setExternalOrderId($data['id']);
        $order->setStatus($data['status']);

        return $order;
    }

    /**
     * @param  OrderObject  $order
     * @param  ShippingAddress|null  $address
     * @param  string|null  $shippingMethod
     * @return OrderObject
     * @throws \Printful\Exceptions\PrintfulApiException
     * @throws \Printful\Exceptions\PrintfulException
     */
    public function estimateCost(OrderObject $order, ?ShippingAddress $address, string $shippingMethod = null): OrderObject
    {
        $response = $this->client->post('orders/estimate-costs', [
            'external_id' => $order->getId(),
            'recipient' => [
                'name' => '', $address->getContact()->getName(),
                'company' => $address->getCompany(),
                'address1' => $address->getAddress1(),
                'address2' => $address->getAddress2(),
                'city' => $address->getCity(),
                'state_code' => $address->getRegion(),
                'country_code' => $address->getCountry(),
                'zip' => $address->getPostalCode(),
                'phone' => $address->getContact()->getPhone(),
                'email' => $address->getContact()->getEmail(),
            ],
            'items' => $order->getItems(),
            'shipping' => $shippingMethod,
        ]);

        $order->setCosts($response['result']['costs']);

        return $order;
    }

    /**
     * @param  OrderObject  $order
     * @param  bool  $confirmed
     * @param  ShippingAddress|null  $address
     * @param  string|null  $shippingMethod
     * @return OrderObject
     * @throws \Printful\Exceptions\PrintfulApiException
     * @throws \Printful\Exceptions\PrintfulException
     */
    public function createOrder(OrderObject $order, bool $confirmed = false, ?ShippingAddress $address = null, string $shippingMethod = null): OrderObject
    {
        $response = $this->client->post('orders', [
            'external_id' => $order->getId(),
            'recipient' => [
                'name' => '', $address->getContact()->getName(),
                'company' => $address->getCompany(),
                'address1' => $address->getAddress1(),
                'address2' => $address->getAddress2(),
                'city' => $address->getCity(),
                'state_code' => $address->getRegion(),
                'country_code' => $address->getCountry(),
                'zip' => $address->getPostalCode(),
                'phone' => $address->getContact()->getPhone(),
                'email' => $address->getContact()->getEmail(),
            ],
            'items' => $order->getItems(),
            'shipping' => $shippingMethod,
            'confirm' => $confirmed,
            'update_existing' => true,
        ]);

        $order->setExternalOrderId($response['result']['id']);
        $order->setCosts($response['result']['costs']);
        $order->setStatus($this->orderStatuses[$response['result']['status']]);

        return $order;
    }

    /**
     * @param  ShippingAddress  $address
     * @param  array  $items
     * @return array
     * @throws \Printful\Exceptions\PrintfulApiException
     * @throws \Printful\Exceptions\PrintfulException
     */
    public function calculateShippingRates(ShippingAddress $address, array $items): array
    {
        $response = $this->client->post('shipping/rates', [
            'recipient' => [
                'address1' => $address->getAddress1(),
                'city' => $address->getCity(),
                'country_code' => $address->getCountry(),
                'state_code' => $address->getRegion(),
                'zip' => $address->getPostalCode(),
            ],
            'items' => $items,
            'currency' => config('commerce.currency'),
        ]);

        return array_map(function ($rate) {
            return new ShippingRate(
                ...Arr::only($rate, ['id', 'name', 'rate', 'currency', 'minDeliveryDays', 'maxDeliveryDays'])
            );
        }, $response['result']);
    }

    public function processWebhook(Request $request)
    {
        $handler = null;
        $service = new PrintfulWebhook($this->client);

        $webhookData = $service->parseWebhook($request->all());

        switch ($webhookData->type) {
            case 'order_updated':
                $handler = new OrderUpdated(
                    $webhookData->type,
                    'printful',
                    $webhookData->order->id,
                    $this->orderStatuses[$webhookData->order->status],
                );
        }

        if ($handler) {
            $handler->handle();
        }
    }
}
