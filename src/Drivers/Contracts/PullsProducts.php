<?php

namespace FiftySq\Commerce\Channels\Drivers\Contracts;

interface PullsProducts
{
    public function getProducts(array $filters = []);

    public function getProduct($productId);

    public function getVariant($variantId);
}
