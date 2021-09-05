<?php

namespace FiftySq\Commerce\Data\Concerns;

use FiftySq\Commerce\Contracts\SkuGeneratorContract;
use Illuminate\Database\Eloquent\Model;

trait HasSku
{
    public static function bootHasSku()
    {
        static::creating(function (Model $model) {
            $model->sku = app(SkuGeneratorContract::class)->generate($model->product, $model);
        });
    }
}
