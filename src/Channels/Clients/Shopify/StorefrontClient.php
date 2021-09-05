<?php

namespace FiftySq\Commerce\Channels\Clients\Shopify;

use FiftySq\Commerce\Data\Models\PendingOrder;
use GraphQL\Client;
use GraphQL\Mutation;
use GraphQL\Variable;

class StorefrontClient extends ShopifyClient
{
    protected const TOKEN_HEADER = 'X-Shopify-Storefront-Access-Token';

    /**
     * @param  PendingOrder  $pending_order
     * @param  string  $notes
     * @return array
     */
    public function createCheckout(PendingOrder $pending_order, $notes = ''): array
    {
        $query = (new Mutation('checkoutCreate'))
            ->setVariables([new Variable('input', 'CheckoutCreateInput!')])
            ->setArguments(['input', '$input'])
            ->setSelectionSet(['id']);

        $values = [
            'input' => [
                'email' => $pending_order->getContactEmail(),
                'lineItems' => [], // $pending_order->items->toArray(),
                'presentmentCurrencyCode' => $pending_order->customer->currency,
                'shippingAddress' => $pending_order->getShippingAddress(),
                'note' => $notes,
            ],
        ];

        return $this->mutate($query, $values);
    }

    /**
     * @param  Mutation  $mutation
     * @param  array  $values
     * @return array
     */
    public function mutate(Mutation $mutation, array $values): array
    {
        $results = $this->client()->runQuery($mutation, true, $values);

        return $results->getData();
    }

    /**
     * Return a client instance.
     *
     * @return Client
     */
    public function client(): Client
    {
        return new Client($this->baseUrl(), $this->headers());
    }

    /**
     * Provide the base URL.
     *
     * @return string
     */
    public function baseUrl(): string
    {
        return "https://{$this->shop}.myshopify.com/api/{$this->version}/graphql";
    }

    /**
     * Build request headers.
     *
     * @return array
     */
    public function headers(): array
    {
        return [
            'Accept' => 'application/json',
            self::TOKEN_HEADER => $this->key,
        ];
    }
}
