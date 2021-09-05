<?php

namespace FiftySq\Commerce;

use FiftySq\Commerce\Data\CommerceMigration;

trait HasProductAttributes
{
    /**
     * Get the casts array.
     *
     * @return array
     */
    public function getCasts()
    {
        $casts = parent::getCasts();

        foreach (CommerceMigration::$productColumns as $column => $schema) {
            $casts[$column] = head($schema);
        }

        return $casts;
    }
}
