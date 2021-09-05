<?php

namespace FiftySq\Commerce\Channels;

use FiftySq\Commerce\Channels\Clients\Shopify\AdminClient;
use FiftySq\Commerce\Channels\Clients\Shopify\PrepareRemoteCheckout;
use FiftySq\Commerce\Channels\Clients\Shopify\StorefrontClient;
use FiftySq\Commerce\Channels\Contracts\HasDiscounts;
use FiftySq\Commerce\Channels\Contracts\PullsProducts;
use FiftySq\Commerce\Channels\Contracts\PushesProducts;
use FiftySq\Commerce\Channels\Contracts\SendsToCheckout;
use FiftySq\Commerce\Data\Models\PendingOrder;

class ShopifyChannel implements HasDiscounts, PullsProducts, PushesProducts, SendsToCheckout
{
    /**
     * @var AdminClient
     */
    protected AdminClient $client;

    /**
     * ShopifyChannel constructor.
     *
     * @param  AdminClient  $client
     */
    public function __construct(AdminClient $client)
    {
        $this->client = $client;
    }

    /**
     * Get a collection of products.
     *
     * @param  array  $filters
     * @return array
     */
    public function getProducts(array $filters = [])
    {
        return $this->client->get('products');
    }

    /**
     * Get a single product.
     *
     * @param $productId
     * @return mixed
     */
    public function getProduct($productId)
    {
        return $this->client->get("products/{$productId}");
    }

    /**
     * Get a single variant.
     *
     * @param $variantId
     * @return mixed
     */
    public function getVariant($variantId)
    {
        return $this->client->get("variants/{$variantId}");
    }

    /**
     * Create a product.
     *
     * @param  array  $values
     * @return mixed
     */
    public function createProduct(array $values)
    {
        return $this->client->post('products', [
            'product' => $values,
        ]);
    }

    /**
     * Update a product.
     *
     * @param $productId
     * @param  array  $values
     * @return mixed
     */
    public function updateProduct($productId, array $values)
    {
        return $this->client->put("products/{$productId}", [
            'product' => $values,
        ]);
    }

    /**
     * Delete a product.
     *
     * @param $productId
     * @return mixed
     */
    public function deleteProduct($productId)
    {
        return $this->client->delete("products/{$productId}");
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

    public function updateVariant($variantId, array $values)
    {
        // TODO: Implement updateVariant() method.
    }

    public function deleteVariant($variantId)
    {
        // TODO: Implement deleteVariant() method.
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
     * Get all discounts for a price rule.
     *
     * @param $ruleId
     * @return mixed
     */
    public function getDiscounts($ruleId)
    {
        return $this->client->get("price_rules/{$ruleId}/discount_codes");
    }

    /**
     * Get a single discount for a price rule.
     *
     * @param $ruleId
     * @param $discountId
     * @return mixed
     */
    public function getDiscount($ruleId, $discountId)
    {
        return $this->client->get("price_rules/{$ruleId}/discount_codes/{$discountId}");
    }

    /**
     * Create a discount for a price rule.
     *
     * @param $ruleId
     * @param  array  $values
     * @return mixed
     */
    public function createDiscount($ruleId, array $values)
    {
        return $this->client->post("price_rules/{$ruleId}/discount_codes", $values);
    }

    /**
     * Update a discount for a price rule.
     *
     * @param $ruleId
     * @param $discountId
     * @param  array  $values
     * @return mixed
     */
    public function updateDiscount($ruleId, $discountId, array $values)
    {
        return $this->client->update("price_rules/{$ruleId}/discount_codes/{$discountId}", $values);
    }

    /**
     * Delete a discount for a price rule.
     *
     * @param $ruleId
     * @param $discountId
     * @return mixed
     */
    public function deleteDiscount($ruleId, $discountId)
    {
        return $this->client->delete("price_rules/{$ruleId}/discount_codes/{$discountId}");
    }

    /**
     * Get all price rules.
     *
     * @return mixed
     */
    public function getPriceRules()
    {
        return $this->client->get('price_rules');
    }

    /**
     * Get a single price rule.
     *
     * @param $ruleId
     * @return mixed
     */
    public function getPriceRule($ruleId)
    {
        return $this->client->get("price_rules/{$ruleId}");
    }

    /**
     * Create a price rule.
     *
     * @param  array  $values
     * @return mixed
     */
    public function createPriceRule(array $values)
    {
        return $this->client->post('price_rules', $values);
    }

    /**
     * Update a price rule.
     *
     * @param $ruleId
     * @param  array  $values
     * @return mixed
     */
    public function updatePriceRule($ruleId, array $values)
    {
        return $this->client->update("price_rules/{$ruleId}", $values);
    }

    /**
     * Delete a price rule.
     *
     * @param $ruleId
     * @return mixed
     */
    public function deletePriceRule($ruleId)
    {
        return $this->client->delete("price_rules/{$ruleId}");
    }

    /**
     * Send the customer to the channel cart for checkout.
     *
     * @param  PendingOrder  $pending_order
     * @return mixed
     */
    public function sendToCheckout(PendingOrder $pending_order)
    {
        $client = app(StorefrontClient::class);

        return $client->mutate('checkouts', (array) new PrepareRemoteCheckout($pending_order));
    }

    /**
     * Complete the channel checkout.
     *
     * @param $token
     * @return mixed
     */
    public function completeCheckout($token)
    {
    }
}
