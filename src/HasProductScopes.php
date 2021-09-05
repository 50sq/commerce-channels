<?php

namespace FiftySq\Commerce;

trait HasProductScopes
{
    /**
     * Scope a query to only include enabled products.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsEnabled($query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('is_enabled', true);
    }
}
