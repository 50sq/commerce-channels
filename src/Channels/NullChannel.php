<?php

namespace FiftySq\Commerce\Channels;

use FiftySq\Commerce\Channels\Contracts\PullsProducts;

class NullChannel implements PullsProducts
{
    public function getProducts(array $filters = [])
    {
        // TODO: Implement getProducts() method.
    }

    public function getProduct($productId)
    {
        // TODO: Implement getProduct() method.
    }

    public function getVariant($variantId)
    {
        // TODO: Implement getVariant() method.
    }
}
