<?php

namespace Tests\Fixtures;

use FiftySq\Commerce\SellableResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    use SellableResource;
}
