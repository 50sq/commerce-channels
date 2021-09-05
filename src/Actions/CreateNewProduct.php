<?php

namespace FiftySq\Commerce\Actions;

use FiftySq\Commerce\Commerce;
use FiftySq\Commerce\Data\Models\Variant;
use FiftySq\Commerce\Data\Product as ProductData;

class CreateNewProduct
{
    /**
     * @param  ProductData  $data
     * @return Variant
     */
    public function __invoke(ProductData $data): Variant
    {
        $product = Commerce::sellable()->create([
            'title' => $data->getTitle(),
            'type' => $data->getType(),
            'is_enabled' => $data->isEnabled(),
            'is_shippable' => $data->isShippable(),
            'is_taxable' => $data->isTaxable(),
        ]);

        return $product->variants()->create([
            'unit_price_cents' => $data->getDefaultPrice(),
        ]);
    }
}
