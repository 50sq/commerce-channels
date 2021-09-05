<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use FiftySq\Commerce\SellableResource;

class ProductResource extends JsonResource
{
    use SellableResource;
}
