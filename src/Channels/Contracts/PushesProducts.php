<?php

namespace FiftySq\Commerce\Channels\Contracts;

interface PushesProducts
{
    public function createProduct(array $values);

    public function updateProduct($productId, array $values);

    public function deleteProduct($productId);

    public function appendProductImages($productId, array $values);

    public function removeProductImages($productId, array $values);

    public function createVariant($productId, array $values);

    public function updateVariant($variantId, array $values);

    public function deleteVariant($variantId);

    public function appendVariantImages($productId, array $values);

    public function removeVariantImages($productId, array $values);
}
