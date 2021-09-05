<?php

namespace FiftySq\Commerce\Data\Models;

use FiftySq\Commerce\Commerce;

class Category extends Model
{
    public function products()
    {
        return $this->hasMany(Commerce::sellableModel(), $this->prefix('category_id'));
    }
}
