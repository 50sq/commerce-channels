<?php

namespace FiftySq\Commerce;

use FiftySq\Commerce\Data\Models\Category;
use FiftySq\Commerce\Data\Models\Variant;

trait HasProductRelationships
{
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function variants()
    {
        return $this->hasMany(Variant::class, Commerce::prefix('product_id'));
    }
}
