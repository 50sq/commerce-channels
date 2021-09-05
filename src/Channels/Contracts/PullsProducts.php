<?php

namespace FiftySq\Commerce\Channels\Contracts;

interface PullsProducts
{
    public function getProducts(array $filters = []);

    public function getProduct($productId);

    public function getVariant($variantId);
}
