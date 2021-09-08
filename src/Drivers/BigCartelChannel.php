<?php

namespace FiftySq\Commerce\Channels\Drivers;

use FiftySq\Commerce\Channels\Drivers\Clients\BigCartelClient;
use FiftySq\Commerce\Channels\Drivers\Contracts\PullsProducts;
use FiftySq\Commerce\Channels\Drivers\Data\Product;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class BigCartelChannel implements PullsProducts
{
    /**
     * @var BigCartelClient
     */
    protected BigCartelClient $client;

    /**
     * BigCartelChannel constructor.
     *
     * @param  BigCartelClient  $client
     */
    public function __construct(BigCartelClient $client)
    {
        $this->client = $client;
    }

    /**
     * @param array $filters
     * @return Collection
     */
    public function getProducts(array $filters = []): Collection
    {
        $products = $this->client->getProducts($filters);

        return collect($products)->map(function ($item) {
            $values = $item['attributes'];

            return new Product(
                ...array_merge([
                    'id' => $item['id'],
                ], Arr::only($values, [
                    'name',
                    'permalink',
                    'description',
                    'default_price',
                    'url',
                    'on_sale',
                    'primary_image_url',
                    'created_at',
                    'updated_at',
                ]))
            );
        });
    }

    /**
     * @param $productId
     * @return mixed
     */
    public function getProduct($productId)
    {
        $product = $this->client->getProduct($productId);

        return $product;
    }

    /**
     * @param $variantId
     * @return mixed
     */
    public function getVariant($variantId)
    {
        return $this->getProduct($variantId);
    }
}
